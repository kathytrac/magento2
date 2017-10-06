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

namespace PluginCompany\ContactForms\Controller\Form;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;
use PluginCompany\ContactForms\Model\EntryFactory;
use PluginCompany\ContactForms\Model\EntryRepository;
use PluginCompany\ContactForms\Model\FormRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context as ContextHelper;
use PluginCompany\ContactForms\Model\VisualCaptchaSession;


class Submit extends Action
{
    CONST CAPTCHA_ERROR = 2;

    protected $resultPageFactory;
    protected $jsonHelper;

    private $contextHelper;
    private $logger;
    private $entry;
    private $entryFactory;
    private $entryRepository;
    private $form;
    private $formRepository;
    private $cleanParams;
    private $customerSession;
    private $visualCaptcha;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param EntryFactory $entryFactory
     * @param EntryRepository $entryRepository
     * @param FormRepository $formRepository
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\App\Helper\Context $contextHelper
     * @param Session $customerSession
     * @param VisualCaptchaSession $vCaptcha
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        EntryFactory $entryFactory,
        EntryRepository $entryRepository,
        FormRepository $formRepository,
        Data $jsonHelper,
        ContextHelper $contextHelper,
        Session $customerSession,
        VisualCaptchaSession $vCaptcha
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->entryFactory = $entryFactory;
        $this->entryRepository = $entryRepository;
        $this->formRepository = $formRepository;
        $this->logger = $contextHelper->getLogger();
        $this->jsonHelper = $jsonHelper;
        $this->contextHelper = $contextHelper;
        $this->customerSession = $customerSession;
        $this->visualCaptcha = $vCaptcha;
        parent::__construct($context);
    }

    /**
     * Execute view action
     */
    public function execute()
    {
        try {
            $this->checkCanContinue();
            $this->runExecute();
        } catch (LocalizedException $e) {
            $this->processError($e);
        } catch (\Exception $e) {
            $this->processError($e);
        }
    }

    public function checkCanContinue()
    {
        if(!$this->isCaptchaOk() ){
            throw new \Exception(__('Captcha error, please complete the challenge'), self::CAPTCHA_ERROR);
        }
        return $this;
    }

    public function runExecute()
    {
        if(!$this->getRequest()->isPost()){
            $this->_redirect('/');
        }
        if($this->shouldNotifyAdmin()){
            $this->notifyAdmin();
        }
        if($this->shouldNotifyCustomer()){
            $this->notifyCustomer();
        }
        if($this->shouldSaveEntry()){
            $this->saveEntry();
        }
        return $this->sendJsonResponseMessage('success', $this->getSuccessMessage());
    }

    public function shouldNotifyAdmin()
    {
        return (bool)$this->getForm()->getNotifyAdmin();
    }

    public function notifyAdmin()
    {
        $this
            ->getEntryWithSubmissionData()
            ->sendAdminNotification()
        ;
        return $this;
    }

    public function shouldNotifyCustomer()
    {
        return (bool)$this->getForm()->getNotifyCustomer();
    }

    public function notifyCustomer()
    {
        $this
            ->getEntryWithSubmissionData()
            ->sendCustomerNotification()
        ;
        return $this;
    }

    public function shouldSaveEntry()
    {
        return $this->getForm()->getEnableEntries();
    }

    /**
     * @return \PluginCompany\ContactForms\Model\Form
     */
    public function getForm()
    {
        if(!$this->form){
            $this->initForm();
        }
        return $this->form;
    }

    public function initForm()
    {
        $this->form = $this->formRepository->getById(
            $this->getFormIdFromParams()
        );
        return $this;
    }

    public function getFormIdFromParams()
    {
        return (int)$this->getRequest()->getParam('form_id');
    }

    public function saveEntry()
    {
        $this->entryRepository->save(
            $this->getEntryWithSubmissionData()
        );
        return $this;
    }

    /**
     * @return \PluginCompany\ContactForms\Model\Entry
     */
    public function getEntryWithSubmissionData()
    {
        $entry = $this->getEntry();
        if(!$entry->getFormId()){
            $entry->initFromSubmittedData($this->getCleanParameters());
        }
        return $entry;
    }

    /**
     * @return \PluginCompany\ContactForms\Model\Entry
     */
    public function getEntry()
    {
        if(!$this->entry){
            $this->initEntry();
        }
        return $this->entry;
    }

    /**
     * @return \PluginCompany\ContactForms\Model\Entry
     */
    private function initEntry()
    {
        $this->entry = $this->entryFactory->create();
        return $this;
    }

    public function getCleanParameters()
    {
        if(empty($this->cleanParams)){
            $this->prepareCleanParams();
        }
        return $this->cleanParams;
    }

    private function prepareCleanParams()
    {
        foreach($this->getParams() as $k => $param){
            $this->cleanParams[$k] = $this->cleanParam($param);
        }
        $this
            ->addFormUrlToParams()
            ->addUploadDirToParams()
        ;
        return $this;
    }

    private function getParams()
    {
        return $this->getRequest()->getParams();
    }

    private function addFormUrlToParams()
    {
        $this->cleanParams['form_url'] = $this->cleanParam($this->getHttpReferer());
        return $this;
    }

    public function cleanParam($param)
    {
        if(is_array($param)){
            $param = implode(', ',$param);
        }
        return htmlspecialchars($param);
    }

    private function addUploadDirToParams()
    {
        $this->cleanParams['upload_dir'] = $this->getUploadDir();
        $this->unsetUploadDirFromSession();
        return $this;
    }

    private function getUploadDir()
    {
        $session = $this->customerSession;
        return $session->getData($this->getSessionDataKeyForForm());
    }

    private function getSessionDataKeyForForm()
    {
        return 'contactforms_upload_key_' . $this->getFormId();
    }

    private function unsetUploadDirFromSession()
    {
        $session = $this->customerSession;
        $session
            ->unsetData($this->getSessionDataKeyForForm())
            ->unsetData($this->getUploadDirSessionDataKey())
        ;
        return $this;
    }

    private function getUploadDirSessionDataKey()
    {
        return 'contactforms_upload_dir_' . $this->getFormId();
    }

    public function getFormId()
    {
        return $this->getEntryWithSubmissionData()->getFormId();
    }

    private function getHttpReferer()
    {
        return $this->getRequest()->getServerValue('HTTP_REFERER');
    }

    public function processError($e)
    {
        $this->logger->critical($e);
        $this->sendJsonResponseMessage(
            'error',
            $e->getMessage(),
            $this->getCodeForError($e)
        );
        return $this;
    }

    public function sendJsonResponseMessage($type, $message, $code = null)
    {
        return $this->jsonResponse([
            'type' => $type,
            'message' => $message,
            'error_code' => $code
        ]);
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    private function getCodeForError($e)
    {
        $code = null;
        if($e->getCode() == self::CAPTCHA_ERROR){
            $code = 'captcha';
        }
        return $code;
    }

    public function getSuccessMessage()
    {
        return $this->getForm()->getFrontendSuccessMessage();
    }

    public function isCaptchaOk()
    {
        return $this->isReCaptchaOk() && $this->isVisualCaptchaOk();
    }

    /**
     * checks whether the submitted captcha text is OK by validating with Google reCaptcha
     * @param $form
     * @return bool
     */
    public function isReCaptchaOk()
    {
        if(!$this->isReCaptchaEnabled()){
            return true;
        }

        try{
            //validate using recaptcha api
            $fields  = 'secret=' . $this->getReCaptchaPrivateKey();
            $fields .= '&response=' . $this->getReCaptchaResponse();
            $fields .= '&remoteip=' . $this->getRemoteAddress();

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_POST, 3);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
            $result = json_decode(curl_exec($ch));
            curl_close($ch);
            return $result->success;
        }catch(Exception $e){
            $this->logger->debug($e);
            return false;
        }
    }

    public function isReCaptchaEnabled()
    {
        return $this->getForm()->hasReCaptcha();
    }

    public function getReCaptchaPrivateKey()
    {
        return $this->getForm()->getReCaptchaPrivateKey();
    }

    public function getReCaptchaResponse()
    {
        $res =  $this->getRequest()->getParam('g-recaptcha-response');
        return $res;
    }

    public function getRemoteAddress()
    {
        return $this->contextHelper->getRemoteAddress()->getRemoteAddress();
    }

    public function isVisualCaptchaOk()
    {
        if(!$this->isVisualCaptchaEnabled()){
            return true;
        }

        return $this->checkVisualCaptcha();
    }

    public function isVisualCaptchaEnabled()
    {
        return $this->getForm()
            ->hasVisualCaptcha();
    }

    public function checkVisualCaptcha()
    {
        $captcha = $this->getVisualCaptcha();
        $frontendData = $captcha->getFrontendData();
        $params = Array();

        $return = 'error';
        $imgField = 'none';
        if ( ! $frontendData ) {
            $return = 'error_nocaptcha';
        } else {
            $imgField =  $frontendData[ 'imageFieldName' ];
            // If an image field name was submitted, try to validate it
            if ( $imageAnswer = $this->getRequest()->getParam( $frontendData[ 'imageFieldName' ] ) ) {
                if ( $captcha->validateImage( $imageAnswer ) ) {
                    $return = 'success';
                } else {
                    $params[] = 'status=failedImage';
                    $return = 'error_image';
                }
            } else if ( $audioAnswer = $this->getRequest()->getParam( $frontendData[ 'audioFieldName' ] ) ) {
                if ( $captcha->validateAudio( $audioAnswer ) ) {
                    $return = 'success';
                } else {
                    $return = 'error_audio';
                }
            }

            $howMany = count( $captcha->getImageOptions() );
            $captcha->generate( $howMany );
        }

        if(stristr($return, 'error')){
            return false;
        }
        return true;
    }

    public function getVisualCaptcha()
    {
        return $this->visualCaptcha->getCaptcha(
            $this->getVisualCaptchaNamespace()
        );
    }

    public function getVisualCaptchaNamespace()
    {
        return $this->getRequest()
            ->getParam('vcaptcha_namespace');
    }
}
