<?php
namespace SendEmailUser;

use MapasCulturais\App;
use MapasCulturais\Entities;
use MapasCulturais\i;

require_once 'vendor/autoload.php';

class Plugin extends \MapasCulturais\Plugin {

    public function __construct(array $config = []) 
    {
        parent::__construct($config);
        
    }

    public function _init() 
    {
        $app = App::i();

        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunity = $this->controller->requestedEntity;
            $type_evaluation = $opportunity->evaluationMethodConfiguration->getDefinition()->slug;
            if ($opportunity->id == '3311' || $opportunity->id == '3313') {
                $opportunity = $this->controller->requestedEntity;
                $this->part('form/button-sendEmailProponent', ['entity' => $opportunity]);
            }
        });
    }

    public function register() 
    {
        $app = App::i();
        $app->registerController('sendEmailProponent', 'SendEmailUser\Controllers\SendEmailProponent');
    }

}

?>