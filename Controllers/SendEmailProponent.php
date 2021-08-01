<?php

namespace SendEmailUser\Controllers;

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class SendEmailProponent extends Controller
{
    public function ALL_sendMailProponent() 
    {
        $app = App::i();

        $listProponents = $app->em->getConnection()->fetchAll("
            select
                r.agents_data,
                r.id as numero_inscricao,
                r.number as num_inscricao_edital,
                case 
                    when am_email_1.value is not null then am_email_1.value
                    else
                        'Dado não informado pelo proponente.'
                end as email_1,
                case 
                    when am_email_2.value is not null then am_email_2.value
                    else
                        'Dado não informado pelo proponente.'
                end as email_2,
                case 
                    when am_nome.value  is not null then am_nome.value 
                    else
                        'Dado não informado pelo proponente.'
                end as nomeCompleto,
                r.status as status_inscricao_edital,
                r.opportunity_id as id_edital,
                op.registration_from as data_abertura_edital,
                op.registration_to as data_fechamento_ediatal
            from 
                public.registration as r 
                    left join public.agent_meta as am_email_1
                        on am_email_1.object_id = r.agent_id
                        and am_email_1.key = 'emailPublico'
                    left join public.agent_meta as am_email_2
                        on am_email_2.object_id = r.agent_id
                        and am_email_2.key = 'emailPrivado'
                    left join public.agent_meta as am_nome
                        on am_nome.object_id = r.agent_id
                        and am_nome.key = 'nomeCompleto'
                    left join public.opportunity as op
                        on op.id = r.opportunity_id
            where
                r.status = 0
                and r.opportunity_id = 3311 or r.opportunity_id = 3313
        ");
        // Select está pegando 2 editais específicos, depois alterar para plugin poder pegar qualquer edital!

        if(!$listProponents) {
            $this->errorJson("Nao foi encontrado proponentes com inscricao em rascunho.");
        }

        foreach ($listProponents as &$proponent) {
            $proponent = (object) $proponent;
            $this->sendMail($proponent);
        }
        
        echo "Todos emails enviados";
    }

    private function sendMail($proponent) 
    {
        $mail= new PHPMailer;

        try {
            $mail->IsSMTP();        // Ativar SMTP
            $mail->SMTPDebug = false;
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPAuth = true;     // Autenticação ativada
            $mail->SMTPSecure = 'ssl';  // SSL REQUERIDO pelo GMail
            $mail->Host = 'smtp.gmail.com'; // SMTP utilizado
            $mail->Port = 465; 
            $mail->Username = 'mapacultural.dev.secult.ce@gmail.com';
            $mail->Password = '#Cetic2021';
            $mail->SetFrom('mapacultural.dev.secult.ce@gmail.com', 'Mapa Cultural CE');
            $mail->addAddress($proponent->email_1);
            $mail->addBCC($proponent->email_2 ? $proponent->email_2 : '' );
            $mail->Subject=("FINALIZE SUA INSCRICAO");
            $mail->msgHTML("
                FINALIZE SUA INSCRIÇÃO NO EDITAL AGENTES DE LEITURA DO CEARÁ!! <br> <br>

                Olá! <br>

                Estamos passando para avisar que você não finalizou a sua inscrição no EDITAL AGENTES DE LEITURA DO CEARÁ – 2021. <br>
                Tem interesse em finalizar seu cadastro? LEMBRE-SE: As inscrições vão até o dia 02/08/2021. Acesse o link para inscrição no MAPA CULTURAL: https://mapacultural.secult.ce.gov.br/oportunidade/$proponent->id_edital/ <br><br>

                Atenciosamente, <br>
                Coordenadoria do Livro, Leitura, Literatura e Biblioteca (CLLB)
            ");
            $mail->send();
        } catch (Exception $e) {
            echo "Falha ao enviar o email - Error: {$mail->ErrorInfo}";
        }
    }
}

?>