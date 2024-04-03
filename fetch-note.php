<?php

require_once(INCLUDE_DIR.'class.signal.php');
require_once(INCLUDE_DIR.'class.plugin.php');
require_once('config.php');

class FetchNotePlugin extends Plugin {
    var $config_class = 'FetchNotePluginConfig';
    private static $instanceConfig = null;

	static $pluginInstance = null;

    private function getPluginInstance(?int $id){
	if($id && ($i = $this->getInstance($id)))
	    return $i;

	return $this->getInstances()->first();
    }


    function bootstrap() {
        self::$pluginInstance = self::getPluginInstance(null);
		
        $pluginInstance = new FetchNotePlugin();
        $pluginInstance->instanceConfig = $this->getConfig();
        
        Signal::connect('ticket.created', array($this, 'onTicketCreated'));
    }

    function onTicketCreated($ticket){
		global $cfg;
		if(!$cfg instanceof OsTicketConfig){
			error_log("FetchNote Plugin calls too early.");
		}

        try {
            $payload = array(
                'email' => $ticket->getEmail(),
            );

            $data_string = utf8_encode(json_encode($payload));
            $url = $this->getConfig(self::$pluginInstance)->get('fetch-note-webhook-url');

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                sprintf('Content-Length: %s', strlen($data_string)),
            ));

            $response = curl_exec($ch);

            if (!$response){
                throw new Exception(sprintf('%s - %s', $url, curl_error($ch)));
            }
            else {
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($statusCode != '200'){
                    throw new Exception(sprintf('%s Http code: %s', $url, $statusCode));
                }
                else {
                    $ticket->logNote('Fetch Note', $response, 'SYSTEM', false);
                }
            }

            curl_close($ch);
        }
        catch(Exception $e) {
            error_log(sprintf('Error posting to Fetch Note Webhook. %s', $e->getMessage()));
        }
    }
}
