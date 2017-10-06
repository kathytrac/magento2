<?php
namespace PluginCompany\ContactForms\Model\lib\Visualcaptcha;
class Session {
    private $namespace = '';

    public function __construct( $namespace = array('namespace' => 'visualcaptcha') ) {
        if(isset($namespace['namespace'])){
            $namespace = $namespace['namespace'];
        }else{
            $namespace = 'visualcaptcha';
        }
        $this->namespace = $namespace;
    }

    public function clear() {
        $_SESSION[ $this->namespace ] = Array();
    }

    public function get( $key ) {
        if ( !isset( $_SESSION[ $this->namespace ] ) ) {
            $this->clear();
        }

        if ( isset( $_SESSION[ $this->namespace ][ $key ] ) ) {
            return $_SESSION[ $this->namespace ][ $key ];
        }

        return null;
    }

    public function set( $key, $value ) {
        if ( !isset( $_SESSION[ $this->namespace ] ) ) {
            $this->clear();
        }
        $_SESSION[ $this->namespace ][ $key ] = $value;
    }
};

?>