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
namespace PluginCompany\ContactForms\Model\Widget;

class SchemaLocatorPlugin
{
    public function afterGetSchema(\Magento\Widget\Model\Config\SchemaLocator $subject, $result)
    {
        $fileName = $this->getEtcDir() . '/widget.xsd';
        return $fileName;
    }

    public function afterGetPerFileSchema(\Magento\Widget\Model\Config\SchemaLocator $subject, $result)
    {
        $fileName = $this->getEtcDir() . '/widget_file.xsd';
        return $fileName;
    }

    private function getEtcDir()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Dir\Reader $reader */
        $reader = $om->get('Magento\Framework\Module\Dir\Reader');
        $etcDir = $reader->getModuleDir('etc', 'PluginCompany_ContactForms');
        return $etcDir;
    }
}