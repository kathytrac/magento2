<?php
/**
 *
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
 *
 */
namespace PluginCompany\ContactForms\Model;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Mail\Template\TransportBuilder;

class Mailer extends TransportBuilder
{

    private $mailData;
    private $entry;

    public function sendCustomerNotification()
    {
        if(!$this->getEntry()){
            $this->throwNoEntryLoadedException();
        }

        $this->initMailData();

        $entry = $this->getEntry();

        $this->mailData
            ->setToEmail(
                $entry->getCustomerEmail()
            )->setToName(
                $entry->getCustomerName()
            )->setBcc(
                explode(',', $entry->getCustomerBcc())
            )->setBody(
                $this->getBodyPrefix() .
                $entry->getCustomerNotification() .
                $this->getBodySuffix()
            )->setSubject(
                $entry->getCustomerSubject()
            )->setFromName(
                $entry->getSenderName()
            )->setFromEmail(
                $entry->getSenderEmail()
            )
            ->setReplyTo(null)
        ;
        $this->sendMail();
        //TODO error handling

        return $this;
    }

    private function throwNoEntryLoadedException()
    {
        throw new \Exception("No entry loaded, unable to send e-mail");
    }

    private function initMailData()
    {
        if(empty($this->mailData)){
            $this->mailData = new DataObject();
        }
        return $this;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
        return $this;
    }

    private function getBodyPrefix()
    {
        return '<html><body style="font-family:calibri,arial,helvetica,sans-serif;font-size:11pt">';
    }

    private function getBodySuffix()
    {
        return '</body></html>';
    }


    public function sendAdminNotification()
    {
        if(!$this->getEntry()){
            $this->throwNoEntryLoadedException();
        }

        $this->initMailData();

        $entry = $this->getEntry();

        $this->mailData
            ->setToEmail(
                $entry->getAdminEmail()
            )->setToName(
                null
            )->setBcc(
                explode(',', $entry->getAdminBcc())
            )->setBody(
                $this->getBodyPrefix() .
                $entry->getAdminNotification() .
                $this->getBodySuffix()
            )->setSubject(
                $entry->getAdminSubject()
            )->setFromName(
                $entry->getAdminSenderName()
            )->setFromEmail(
                $entry->getAdminSenderEmail()
            )
            ->setReplyTo(null)
        ;
        $this->sendMail();

        return $this;
    }

    /**
     * Send Customer Comment e-mail message
     *
     * @param Entry $entry
     * @return $this|bool
     */
    public function sendCommentNotification(Entry $entry, $comment)
    {
        $form = $entry->getParentForm();
        $params = $entry->getParamsArray();

        $params['comment_text'] = nl2br($comment);
        $params['customer_name'] = $entry->getCustomerName();

        //general wrapper for font style
        $bodyPrefix = '<html><body style="font-family:calibri,arial,helvetica,sans-serif;font-size:11pt">';
        $bodySuffix = '</body></html>';

        $mailData = [];

        //use name or else firstname and lastname as recipient title
        $mailData['to_name'] = $entry->getCustomerName();
        $mailData['to_email'] = $entry->getCustomerEmail();
        $mailData['from_name'] = $form->getMailVar('customer_from_name');
        $mailData['from_email'] = $form->getMailVar('customer_from_email');
        $mailData['body'] = $bodyPrefix . $this->_getCommentTemplate($entry->getStoreId()) . $bodySuffix;
        $mailData['subject'] = $this->_getCommentNotificationSubject($entry->getStoreId());
        $mailData['bcc'] = $form->getMailVar('customer_mail_bcc');

        //insert variables into e-mail data
        $mailData = $this->insertVariables($mailData,$params);

        //create parameter object
        $paramsObj = new DataObject();
        $paramsObj
            ->setData($mailData)
            ->setCanSendMail(true);

        //event hook
        $this->_eventManagerInterface->dispatch('pc_contactforms_comment_notify_before', ['entry' => $entry, 'comment' => $comment]);

        //send e-mail
        if($paramsObj->getCanSendMail()){
            $mailData = $paramsObj->getData();
            $this->sendMail($mailData);

            $this->_eventManagerInterface->dispatch('pc_contactforms_comment_notify_after', ['entry' => $entry, 'comment' => $comment]);
        }

        return $this;

    }

    public function sendMail()
    {
        $mailData = $this->mailData;
        $message = $this->message;
        $message
            ->setBody($mailData->getBody())
            ->setSubject($mailData->getSubject())
            ->setFrom($mailData->getFromEmail(), $mailData->getFromName())
            ->setReplyTo($mailData->getReplyTo())
            ->addBcc($this->getBcc())
            ->addTo($this->getToEmail(), $mailData->getToName())
        ;
        $this->getTransport()->sendMessage();
        return $this;
    }

    public function getToEmail()
    {
        return $this->explodeIfCommaDelimited(
            $this->mailData->getData('to_email')
        );
    }

    public function getBcc()
    {
        return $this->explodeIfCommaDelimited(
            $this->mailData->getData('bcc')
        );
    }

    private function explodeIfCommaDelimited($mail)
    {
        if(!is_array($mail) && stristr($mail,',')){
            return explode(',', $mail);
        }
        return $mail;
    }

    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        try{
            $this->message
                ->setMessageType('text/html');
            $this->message
                ->setBodyHtml($this->mailData->getBody());
        }catch(\Exception $e){
            $bla = 1;
        }
        return $this;
    }

    protected function _getCommentTemplate($storeId)
    {
        return $this->_configScopeConfigInterface->getValue('plugincompany_contactforms/customer_notification/comment_template', ScopeInterface::SCOPE_STORE,$storeId);
    }

    protected function _getCommentNotificationSubject($storeId)
    {
        return $this->_configScopeConfigInterface->getValue('plugincompany_contactforms/customer_notification/comment_subject', ScopeInterface::SCOPE_STORE,$storeId);
    }

}
