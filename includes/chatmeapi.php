<?php
/*
Questa Classe non è estendibile e permette di avere accesso in modo chiaro alle API di ChatMe
$service può essere:
	connected
	muc-log.internal
	muc.internal
	register-web
	url-connessi-muc
	url-status
	webchat
esempio: $host = ChatMeApi::getHost($dominio); fornirà l'host di registrazione del dominio
esempio: $host = (new ChatMeApi)->getHost($dominio); fornirà l'host di registrazione del dominio		
*/

final class ChatMeApi{
	
	static function getHost($dominio='chatme.im',$service='register-web',$protocol='https://') {
    		$data = dns_get_record($service.'.internal.'.$dominio,DNS_TXT,$authns);
    		$host = $protocol . $data[0]['txt'];
    		return $host;
	}
	
}	
?>