<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 * 
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 * 
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 * 
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 * 
 * SUPPORT@PLUGIN.COMPANY
 */

namespace PluginCompany\ContactForms\Model;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Element\BlockFactory;
use PluginCompany\ContactForms\Api\Data\EntryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use PluginCompany\ContactForms\Model\Entry as ModelEntry;
use PluginCompany\ContactForms\Model\FormRepository;
use PluginCompany\ContactForms\Model\Mailer;
use PluginCompany\ContactForms\Model\Template\Filter;

class Entry extends \Magento\Framework\Model\AbstractModel implements EntryInterface
{
    /**    * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'plugincompany_contactforms_entry';
    const CACHE_TAG = 'plugincompany_contactforms_entry';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'plugincompany_contactforms_entry';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'entry';

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FormRepository
     */
    private $formRepository;

    private $mailer;
    private $filter;
    private $submissionParams;
    private $blockFactory;
    private $ioFile;
    private $directoryList;


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PluginCompany\ContactForms\Model\ResourceModel\Entry');
    }

    public function __construct(Context $context,
        Registry $registry,
        DateTime $date,
        StoreManagerInterface $storeManagerInterface,
        FormRepository $formRepository,
        Mailer $mailer,
        Filter $filter,
        BlockFactory $blockFactory,
        DirectoryList $directoryList,
        File $ioFile,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->date = $date;
        $this->storeManager = $storeManagerInterface;
        $this->formRepository = $formRepository;
        $this->mailer = $mailer;
        $this->filter = $filter;
        $this->blockFactory = $blockFactory;
        $this->ioFile = $ioFile;
        $this->directoryList = $directoryList;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve parent
     * @access public
     * @return null|Form
     * @author Milan Simek
     */
    public function getForm(){
        if (!$this->hasData('_parent_form')) {
            if (!$this->getFormId()) {
                return null;
            }
            else {
                $form = $this->formRepository->getById($this->getFormId());
                if ($form->getId()) {
                    $this->setData('_parent_form', $form);
                }
                else {
                    $this->setData('_parent_form', null);
                }
            }
        }
        return $this->getData('_parent_form');
    }

    public function initFromSubmittedData($params)
    {
        $this
            ->setSubmissionParams($params)
            ->initFormIdFromParams()
            ->initFilterParams()
        ;

        $this->setData(
            [
                'form_id' => $this->getForm()->getId(),
                'store_id'=> $this->storeManager->getStore()->getStoreId(),
                'customer_name' => $this->getCustomerName(),
                'customer_email' => $this->getCustomerEmail(),
                'customer_bcc'=> $this->getCustomerBcc(),
                'sender_name'=> $this->getSenderName(),
                'sender_email'=> $this->getSenderEmail(),
                'customer_subject'=> $this->getCustomerSubject(),
                'customer_notification'=> $this->getCustomerNotification(),
                'admin_email'=> $this->getAdminEmail(),
                'admin_bcc'=> $this->getAdminBcc(),
                'admin_notification'=> $this->getAdminNotification(),
                'admin_subject'=> $this->getAdminSubject(),
                'admin_sender_name'=> $this->getAdminSenderName(),
                'admin_sender_email'=> $this->getAdminSenderEmail(),
                'admin_reply_to_email'=> $this->getAdminReplyToEmail(),
                'fields'=> $this->getSubmissionParams()->toJson(),
                'increment_id' => $this->getForm()->getNextEntryIncrementIdCounter(),
                'increment_text' => $this->getForm()->getNextIncrementText(),
                'upload_dir' => $this->getUploadDir()
            ]
        );
        return $this;
    }

    public function setSubmissionParams($params)
    {
        $params = new DataObject($this->utf8encodeArray($params));
        $this->setUploadDir($params->getUploadDir());
        $this->removeUnneededParams($params);
        $this->submissionParams = $params;
        return $this;
    }

    private function removeUnneededParams(DataObject $params)
    {
        $params
            ->unsetData('uid')
            ->unsetData('submitform')
            ->unsetData('upload_dir')
            ->unsetData('g-recaptcha-response')
        ;
    }

    public function getSubmissionParams()
    {
        return $this->submissionParams;
    }

    private function utf8encodeArray($params)
    {
        foreach($params as $k => $v)
        {
            $params[$k] = utf8_encode($v);
        }
        return $params;
    }

    private function initFilterParams()
    {
        $variables = [];
        $variables['submission'] = $this->getSubmissionParams();
        $variables['submission_overview'] = '##submission_overview##';
        $variables = array_merge($variables, $this->getSubmissionParams()->toArray());
        $this->filter
            ->setVariables($variables);
        return $this;
    }

    public function addSubmissionTableToString($html)
    {
        $table = $this->getSubmissionOverviewTable();
        return str_replace(
            '##submission_overview##',
            $table,
            $html
        );
    }

    public function getSubmissionOverviewTable()
    {
        return $this->blockFactory
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('PluginCompany_ContactForms::email/submission_overview_table.phtml')
            ->setSubmissionParams($this->getFilteredSubmissionParamsForTable())
            ->toHtml()
        ;
    }

    private function getFilteredSubmissionParamsForTable()
    {
        $params = [];
        foreach($this->getSubmissionParams()->getData() as $key => $param){
            $params[$key] = $this->filterVar($param);
        }
        return $params;
    }

    private function initFormIdFromParams()
    {
        $this->setFormId(
            $this->getSubmissionParams()->getFormId()
        );
        return $this;
    }

    public function getCustomerName()
    {
        if(!$this->getData('customer_name')){
            $this->initCustomerName();
        }
        return $this->getData('customer_name');
    }

    private function initCustomerName()
    {
        $this->setCustomerName(
            $this->getCustomerNameFromSubmission()
        );
        return $this;
    }

    private function getCustomerNameFromSubmission()
    {
        if ($val = $this->getForm()->getCustomerToName()) {
            return $this->filterVar($val);
        }
        $params = $this->getSubmissionParams();
        if ($val = $params->getName()) {
            return $val;
        }
        if($params->getFirstname()) {
            return $params->getFirstname() . ' ' . $params->getLastname();
        }

    }

    public function getCustomerEmail()
    {
        if(!$this->getData('customer_email')){
            $this->initCustomerEmail();
        }
        return $this->getData('customer_email');
    }

    private function initCustomerEmail()
    {
        $email = $this->getSubmissionParams()->getEmail();
        if ($val = $this->getForm()->getCustomerToEmail()) {
            $email = $this->filterVar($val);
        }
        $this->setCustomerEmail($email);
        return $this;
    }

    public function getCustomerBcc()
    {
        return $this->getFilteredSubmissionFieldValue(
            'customer_bcc',
            'customer_mail_bcc'
        );
    }

    public function getSenderName()
    {
        return $this->getFilteredSubmissionFieldValue(
            'sender_name',
            'customer_from_name'
        );
    }

    public function getSenderEmail()
    {
        return $this->getFilteredSubmissionFieldValue(
            'sender_email',
            'customer_from_email'
        );
    }

    public function getCustomerSubject()
    {
        return $this->getFilteredSubmissionFieldValue(
            'customer_subject',
            'customer_mail_subject'
        );
    }

    public function getCustomerNotification()
    {
        $html = $this->getFilteredSubmissionFieldValue(
            'customer_notification',
            'customer_mail_content'
        );
        return $this->addSubmissionTableToString($html);
    }

    public function getAdminEmail()
    {
        if($email = $this->getData('admin_email')){
            return $email;
        }

        $emails = $this->getConditionalAdminToEmails();
        if(!empty($emails)){
            return implode(',', $emails);
        }

        return $this->getFilteredSubmissionFieldValue(
            'admin_email',
            'admin_to_email'
        );
    }

    private function getConditionalAdminToEmails()
    {
        return $this->getForm()
            ->getConditionalAdminToEmail(
                $this->getSubmissionParams()
            );
    }

    public function getAdminBcc()
    {
        return $this->getFilteredSubmissionFieldValue(
            'admin_bcc',
            'admin_mail_bcc'
        );
    }

    public function getAdminNotification()
    {
        $html = $this->getFilteredSubmissionFieldValue(
            'admin_notification',
            'admin_notification_content'
        );
        return $this->addSubmissionTableToString($html);
    }

    public function getAdminSubject()
    {
        return $this->getFilteredSubmissionFieldValue(
            'admin_subject',
            'admin_mail_subject'
        );
    }

    public function getAdminSenderName()
    {
        return $this->getFilteredSubmissionFieldValue(
            'admin_sender_name',
            'admin_from_name'
        );
        return $this;
    }

    public function getAdminSenderEmail()
    {
        return $this->getFilteredSubmissionFieldValue(
            'admin_sender_email',
            'admin_from_email'
        );
        return $this;
    }

    public function getAdminReplyToEmail()
    {
        return $this->getFilteredSubmissionFieldValue(
            'admin_reply_to_email',
            'admin_reply_to_email'
        );
        return $this;
    }

    private function getFilteredSubmissionFieldValue($key, $formKey)
    {
        if(!$this->getData($key)){
            $this->initSubmissionParameterFromForm($key, $formKey);
        }
        return $this->getData($key);
    }

    private function initSubmissionParameterFromForm($key, $formKey)
    {
        $this->setData($key,
            $this->filterVar(
                $this->getForm()->{$this->getCamelCaseGetString($formKey)}()
            )
        );
        return $this;
    }

    private function getCamelCaseGetString($string)
    {
        return 'get' . str_replace('_','', ucwords($string, '_'));
    }

    private function filterVar($value)
    {
        return $this->filter->filter($value);
    }


    /**
     * get default values
     * @access public
     * @return array
     * @author Milan Simek
     */
    public function getDefaultValues() {
        $values = [];
        $values['status'] = 1;
        return $values;
    }

    public function sendCustomerNotification()
    {
        if(!$this->isInitialized()){
            $this->throwEmailException();
        }
        try{
            $this->sendCustomerNotificationEmail();
        }
        catch(\Exception $e){
            $this->processEmailNotificationError($e);
        }
        return $this;
    }

    public function sendAdminNotification()
    {
        if(!$this->isInitialized()){
            $this->throwEmailException();
        }
        try{
            $this->sendAdminNotificationEmail();
        }
        catch(\Exception $e){
            $this->processEmailNotificationError($e);
        }
        return $this;
    }

    public function isInitialized()
    {
        return (bool)$this->getFormId();
    }

    private function throwEmailException()
    {
        throw new \Exception('Can\'t send e-mail because because no submission data is set');
    }

    private function sendCustomerNotificationEmail()
    {
        $this->mailer
            ->setEntry($this)
            ->sendCustomerNotification()
        ;
        $this->setCustomerNotificationSent(1);
        return $this;
    }

    private function processEmailNotificationError($e)
    {
        //todo
    }

    private function sendAdminNotificationEmail()
    {
        $this->mailer
            ->setEntry($this)
            ->sendAdminNotification()
        ;
        $this->setAdminNotificationSent(1);
        return $this;
    }

    public function getAllUploads()
    {
        if(!$this->getUploadDir()){
            return [];
        }
        return $this->retrieveUploadsList();
    }

    private function retrieveUploadsList()
    {
        $dir = $this->getUploadBaseDir() . $this->getUploadDir();
        $io = $this->ioFile;
        $io->cd($dir);
        return $io->ls();
    }

    private function getUploadBaseDir()
    {
        $ds = DIRECTORY_SEPARATOR;
        return $this->getMediaDir() . $ds . 'contactforms' . $ds . 'uploads' . $ds;
    }

    private function getMediaDir()
    {
        return $this->directoryList->getPath('media');
    }

}
