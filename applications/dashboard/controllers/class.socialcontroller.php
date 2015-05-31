<?php
/**
 * Manages the social plugins.
 *
 * @copyright 2008-2015 Vanilla Forums, Inc
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package Dashboard
 * @since 2.1
 */

/**
 * Handles /social endpoint, so it must be an extrovert.
 */
class SocialController extends DashboardController {

    /** @var array Models to automatically instantiate. */
    public $Uses = array('Form', 'Database');

    /**
     * Runs before every call to this controller.
     */
    public function Initialize() {
        parent::Initialize();
        Gdn_Theme::Section('Dashboard');
    }

    /**
     * Default method.
     */
    public function Index() {
        Redirect('social/manage');
    }

    /**
     * Settings page.
     */
    public function Manage() {
        $this->Permission('Garden.Settings.Manage');
        $this->Title("Social Integration");
        $this->AddSideMenu('dashboard/social');

        $Connections = $this->GetConnections();
        $this->SetData('Connections', $Connections);

        $this->Render();
    }

    /**
     * Find available social plugins.
     *
     * @return array|mixed
     * @throws Exception
     */
    protected function GetConnections() {
        $this->FireEvent('GetConnections');
        $Connections = $this->Data('Connections', array());
        if (!is_array($Connections)) $Connections = array();

        foreach (Gdn::PluginManager()->AvailablePlugins() as $PluginKey => $PluginInfo) {
            if (!array_key_exists('SocialConnect', $PluginInfo)) continue;

            if (!array_key_exists($PluginKey, $Connections))
                $Connections[$PluginKey] = array();

            $ConnectionName = $PluginInfo['Index'];

            if (Gdn::PluginManager()->CheckPlugin($PluginKey)) {
                $Configured = Gdn::PluginManager()->GetPluginInstance($ConnectionName, Gdn_PluginManager::ACCESS_PLUGINNAME)->IsConfigured();
            } else {
                $Configured = NULL;
            }

            $Connections[$PluginKey] = array_merge($Connections[$PluginKey], $PluginInfo, array(
                'Enabled' => Gdn::PluginManager()->CheckPlugin($PluginKey),
                'Configured' => $Configured
            ), array(
                'Icon' => sprintf("/plugins/%s/icon.png", $PluginInfo['Folder'])
            ));
        }

        return $Connections;
    }

    /**
     * Turn off a social plugin.
     *
     * @param $Plugin
     * @throws Exception
     */
    public function Disable($Plugin) {
        $this->Permission('Garden.Settings.Manage');
        $Connections = $this->GetConnections();
        unset($this->Data['Connections']);

        if (!array_key_exists($Plugin, $Connections)) {
            throw NotFoundException('SocialConnect Plugin');
        }

        Gdn::PluginManager()->DisablePlugin($Plugin);

        $Connections = $this->GetConnections();
        $Connection = GetValue($Plugin, $Connections);

        require_once($this->FetchViewLocation('connection_functions'));
        ob_start();
        WriteConnection($Connection);
        $Row = ob_get_clean();

        $this->JsonTarget("#Provider_{$Connection['Index']}", $Row);
        $this->InformMessage(T("Plugin disabled."));

        unset($this->Data['Connections']);
        $this->Render('blank', 'utility');
    }

    /**
     * Turn on a social plugin.
     *
     * @param $Plugin
     * @throws Exception
     * @throws Gdn_UserException
     */
    public function Enable($Plugin) {
        $this->Permission('Garden.Settings.Manage');
        $Connections = $this->GetConnections();

        if (!array_key_exists($Plugin, $Connections)) {
            throw NotFoundException('SocialConnect Plugin');
        }

        Gdn::PluginManager()->EnablePlugin($Plugin, NULL);

        $Connections = $this->GetConnections();
        $Connection = GetValue($Plugin, $Connections);

        require_once($this->FetchViewLocation('connection_functions'));
        ob_start();
        WriteConnection($Connection);
        $Row = ob_get_clean();

        $this->JsonTarget("#Provider_{$Connection['Index']}", $Row);
        $this->InformMessage(T("Plugin enabled."));

        unset($this->Data['Connections']);
        $this->Render('blank', 'utility');
    }

}
