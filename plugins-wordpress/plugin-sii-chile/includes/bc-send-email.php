<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-helpers.php';
require_once BC_PLUGIN_DIR_PATH . 'vendor/phpmailer/src/Exception.php';
require_once BC_PLUGIN_DIR_PATH . 'vendor/phpmailer/src/PHPMailer.php';
require_once BC_PLUGIN_DIR_PATH . 'vendor/phpmailer/src/SMTP.php';

define( 'BC_USERNAME_MAIL', 'no-reply@lars.com.co');
define( 'BC_P_EMAIL', 'Soporte2011*');
define( 'BC_EMAIL_HOST', 'reseller12.prodns.com.co');

class BC_Send_Email{

    private $db;
    private $db_results;

    /**
     * @author Matías
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->db_results = BC_TABLE_RESULTS_ANALYTICS;
    }

    /**
     * @author
     */
    public function principal_mail($user, $email){

        $data_best_img = $this->search_reports();     
        $loc_reports   = $this->read_location($data_best_img);
        $this->send_($user, $email, $loc_reports);
    }

    /**
     * @author
     */
    public function search_reports(){

        // consulta en BD para obtener los datos de las 5 imágenes más vistas segun el promedio de vista
        $query = "SELECT * FROM $this->db_results ORDER BY promedio DESC LIMIT 5";
        $results = $this->db->get_results($query);        
        return $results;
    }

    /**
     * @author
     */
    public function read_location($data){
        
        // Se busca la ubicación exacta en donde se encuentran los informes de las 5
        // imágenes más vistas
        $a = 0;
        foreach($data as $d){
            $reports_[$a] = [
                'pdf_file' => $d->pdf_producto,
                'image'    => $d->producto
            ];
            $a++;
        }
        return $reports_;
    }

    /**
     * @author
     */
    public function send_($user, $email, $data_reports){

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
            $mail->isSMTP();
            $mail->Host = BC_EMAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            $mail->Username = BC_USERNAME_MAIL; // YOUR gmail email
            $mail->Password = BC_P_EMAIL; // YOUR gmail password
        
            // Sender and recipient settings
            $mail->setFrom(BC_USERNAME_MAIL, 'BillConnector');
            $mail->addAddress($email, 'Receiver Name');
            $mail->addReplyTo(BC_USERNAME_MAIL, 'Sender Name'); // to set the reply to
        
            // Setting the email content
            $mail->IsHTML(true);
            $mail->Subject = "Hola $user, tu resumen diario de BillConnector.";
            $mail->Body = '<table style="max-width: 600px; padding: 10px; margin:0 auto; border-collapse: collapse;">';
            $mail->Body .= '<tr>';
            $mail->Body .= '<td style="background-color: #fff; text-align: left; padding: 0">';
            $mail->Body .= '</td>';
            $mail->Body .= '</tr>';

            $mail->Body .= '<tr>';
            $mail->Body .= '<td style="padding: 0">';
            $mail->Body .= '<img style="padding: 0; display: block" src="https://billconnector.com/wp-content/uploads/2021/06/logo1@2x.png" height="120px">';
            $mail->Body .= '</td>';
            $mail->Body .= '</tr>';

            $mail->Body .= '<tr>';
            $mail->Body .= '<td style="background-color: #fff">';
            $mail->Body .= '<div style="color: #011936; margin: 4% 10% 2%; text-align: justify;font-family: Baloo 2">';
            $mail->Body .= '<h2 style="color: #00a8e8; margin: 0 0 7px">Hola '. $user . '!</h2>';
            $mail->Body .= '<p style="margin: 2px; font-size: 15px">

                             Estos son los informes de las 5 imágenes más vistas de 
                             tu sitio de Wordpress.</p>';
            $mail->Body .= '<div style="width: 100%;margin:20px 0; display: inline-block;text-align: center">';
            $mail->Body .= '</div>';
            $mail->Body .= '<div style="width: 100%; text-align: center">';
            $mail->Body .= '<a style="text-decoration: none; border-radius: 5px; padding: 11px 23px; color: white; background-color: #3498db" href="https://billconnector.com">Visitar BillConnector</a>	';
            $mail->Body .= '</div>';
            $mail->Body .= '<p style="color: #b3b3b3; font-size: 12px; text-align: center;margin: 30px 0 0">BillConnector</p>';
            $mail->Body .= '</div>';
            $mail->Body .= '</td>';
            $mail->Body .= '</tr>';
            $mail->Body .= '</table>';


            foreach($data_reports as $path){
                $mail->AddAttachment(BC_PLUGIN_DIR_PATH . $path['pdf_file']);
            }

            $mail->send();


            write_log("Mensaje enviado");

        } catch (Exception $e) {
            write_log("Error al enviar email. Error: $e");
        }
    }
}

