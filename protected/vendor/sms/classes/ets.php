<?php
    namespace compeek\ets {
        class Exception extends \Exception {
            public function __toString() {
                return 'Email to SMS Error: ' . $this->message;
            }
        }

        class gateway {
            private static $defaultInstance;

            public static $countries;
            public static $carriers;


            public static function initialize() {
                if(!class_exists('Swift'))
                    require realpath(dirname(__FILE__) . '/../dependencies/swift_mailer/swift_required.php'); // load Swift Mailer library


                /* Load countries */

                require(realpath(dirname(__FILE__) . '/../definitions/countries.php'));

                uasort( // alphabetize by name
                    $countries,
                    array(get_class(), 'alphabetizeByName')
                );

                self::$countries = $countries;


                /* Load carriers */

                require(realpath(dirname(__FILE__) . '/../definitions/carriers.php'));

                uasort( // alphabetize by name
                    $carriers,
                    array(get_class(), 'alphabetizeByName')
                );

                self::$carriers = $carriers;
            }

            private static function alphabetizeByName(array $a, array $b) {
                return strcasecmp($a['name'], $b['name']);
            }

            public static function sendSMS($fromEmail, $toNumber, $toCarrier, $subject, $body) {
                if(is_null(self::$defaultInstance))
                    self::$defaultInstance = new gateway();

                $message = new sms($fromEmail, $subject, $body);
                $message->addRecipient($toNumber, $toCarrier);

                self::$defaultInstance->send($message);
            }

            public static function sendMMS($fromEmail, $toNumber, $toCarrier, $subject, $body, $attachmentFilePath = null) {
                if(is_null(self::$defaultInstance))
                    self::$defaultInstance = new gateway();

                $message = new mms($fromEmail, $subject, $body);
                $message->addRecipient($toNumber, $toCarrier);
                if(!is_null($attachmentFilePath))
                    $message->addAttachment($attachmentFilePath);

                self::$defaultInstance->send($message);
            }


            private $smMailer; // Swift Mailer mailer


            public function __construct(\Swift_Transport $smTransport = null) {
                $this->smMailer = \Swift_Mailer::newInstance(!is_null($smTransport) ? $smTransport : \Swift_MailTransport::newInstance()); // create Swift Mailer mailer, defaulting to mail transport
            }

            public function send(message $message) {
                $email = \Swift_Message::newInstance()
                    ->setFrom($message->fromEmail)
                    ->setSender($message->fromEmail)
                    ->setReplyTo($message->fromEmail)
                    ->setReturnPath($message->fromEmail)
                    ->setSubject($message->subject)
                    ->setBody($message->body, 'text/plain')
                    ->setEncoder(\Swift_Encoding::getQpEncoding()) // use quoted-printable content-transfer encoding
                    ->setCharset($message::type == 'sms' ? 'utf8' : 'ISO-8859-1');

                // Add any attachments
                if($message::type == 'mms' && count($message->attachments))
                    foreach($message->attachments as $attachment)
                        $email->attach(\Swift_Attachment::fromPath($attachment));

                foreach($message->recipients as $recipient) {
                    $email->setTo($recipient['number'] . '@' . self::$carriers[$recipient['carrier']]['domains'][$message::type]);

                    $this->smMailer->send($email);
                }
            }
        } gateway::initialize(); // call emulated static constructor

        abstract class message {
            public function __construct($fromEmail, $subject, $body) {
                $this->fromEmail = $fromEmail;
                $this->subject = $subject;
                $this->body = $body;
            }

            public function addRecipient($number, $carrier) {
                if(!isset(gateway::$carriers[$carrier])) // if carrier is not known
                    throw(new Exception('\'' . $carrier . '\' is not a valid carrier code.'));

                $country = gateway::$carriers[$carrier]['country'];

                // Reformat number for email address
                $formattedNumber = str_replace(array(' ', '(', ')', '-', '.'), '', $number); // remove any spaces, parentheses, dashes, or dots
                $formattedNumber = preg_replace('/^(?:\+?' . gateway::$countries[$country]['countryCode'] . '|' . gateway::$countries[$country]['trunkPrefix'] . ')?([0-9]{' . gateway::$countries[$country]['numberLength'] . '})$/', gateway::$carriers[$carrier]['prefix'] . '$1', $formattedNumber, 1, $numberIsValid);
                if(!$numberIsValid)
                    throw(new Exception('\'' . $number . '\' is not a valid phone number for ' . gateway::$carriers[$carrier]['name'] . ' (' . gateway::$countries[$country]['name'] . ').'));

                $this->recipients[] = array(
                    'number' => $formattedNumber,
                    'carrier' => $carrier
                );
            }
        }

        class sms extends message {
            const type = 'sms';


            public $fromEmail;
            public $subject;
            public $body;

            public $recipients = array();
        }

        class mms extends message {
            const type = 'mms';


            public $fromEmail;
            public $subject;
            public $body;
            public $attachments = array();

            public $recipients = array();


            public function addAttachment($filePath) {
                $this->attachments[] = $filePath;
            }

            public function addRecipient($number, $carrier) {
                parent::addRecipient($number, $carrier);

                if(!isset(gateway::$carriers[$carrier]['domains']['mms'])) // if MMS is not supported for recipient's carrier
                    throw(new Exception('MMS is not currently supported for ' . gateway::$carriers[$carrier]['name'] . ' (' . gateway::$countries[isset(gateway::$carriers[$carrier]['country']) ? gateway::$carriers[$carrier]['country'] : 'us']['name'] . ').')); // default to 'us' for country
            }
        }
    }
?>