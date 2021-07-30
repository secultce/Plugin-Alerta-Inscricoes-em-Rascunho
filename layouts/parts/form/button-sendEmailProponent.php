<?php
use MapasCulturais\App;
use MapasCulturais\i;

$route = App::i()->createUrl('sendEmailProponent', 'sendMailProponent', ['id'=>$entity->id]);

?>

<a class="btn btn-default" ng-click="editbox.open('report-evaluation-technical-options', $event)"
    rel="noopener noreferrer">Enviar email para proponentes</a>

<edit-box id="report-evaluation-technical-options" position="top"
    title="<?php i::esc_attr_e('Enviar emails')?>"
    cancel-label="Cancelar" close-on-cancel="true">

    <form class="form-report-evaluation-technical-options"
        action="<?=$route?>" method="GET">
        <p>Enviar email para proponentes que estão com a inscrição em rascunho.</p>
        <button class="btn btn-primary" type="submit">Enviar</button>
    </form>
</edit-box>
