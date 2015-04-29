<?
class pop3
{
    private $rConnection, $sUser, $sPass, $sHost, $iPort, $sReturn;
    public function __construct($sHost,$sUser,$sPass,$iPort=110)
    {
        $this->sHost = $sHost;
        $this->iPort = $iPort;
        $this->sUser = $sUser;
        $this->sPass = $sPass;
        $this->rConnection = fsockopen($sHost,$iPort,$iErrorNum, $sErrorStr,5);
        if(substr(trim($this->sReturn = $this->stream_get_contents() ),0,3) != '+OK')
        {
            $this->throwError('fsockopen:'.$sErrorStr);
            return false;
        }
        $this->toSock('USER '.$sUser);
        $this->sReturn = trim($this->stream_get_contents());
        if(substr($this->sReturn,0,3) != '+OK')
        {
            return false;
        }
        $this->toSock('PASS '.$sPass);
        $this->sReturn = trim($this->stream_get_contents());
        if(substr($this->sReturn,0,3) != '+OK')
        {
            return false;
        }
        return true;
    }
    public function __destruct()
    {
        $this->toSock('QUIT');
        fclose($this->rConnection);
    }
    private function stream_get_contents($iLength = 512, $sStopAfter = '')
    {
        $sReturn = fread($this->rConnection,$iLength);
		$sReturn = str_replace("\r\n","\n",$sReturn);
        if($iLength > 1000){
            while (!feof($this->rConnection)) { 
                $sReturn .= fread($this->rConnection, 1024);
				$sReturn = str_replace("\r\n","\n",$sReturn);
				if($sStopAfter != '' && strpos($sReturn, $sStopAfter) !== false){
					$sReturn = explode($sStopAfter,$sReturn, 2)[0];
					break;
				}
				else {
					$stream_meta_data = stream_get_meta_data($this->rConnection); //Added line
					//print_r($stream_meta_data);
					if($stream_meta_data['unread_bytes'] <= 0) break; //Added line
				}
            }
        }
        return $sReturn;
    }
    private function toSock($sStr)
    {
        if($this->rConnection)
        {
            if(fwrite($this->rConnection,$sStr."\n")===FALSE)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            $this->throwError('Geen verbinding');
            return false;
        }
    }
    private function throwError($sError)
    {
        echo '<br>
        <hr>
        Er is een fout opgetreden.<br>
        De volgende fout werd meegegeven:<br>'.$sError.'<hr>
        <br>';
    }
    public function deleteMessage($iBerichtNummer)
    {
        $this->toSock('DELE '.$iBerichtNummer);
        $this->sReturn = trim($this->stream_get_contents());
        return (substr($this->sReturn,0,3) == '+OK');
    }
    public function listMessages($iBerichtNummer = '')
    {
        if(!empty($iBerichtNummer))
        {
            if(!is_numeric($iBerichtNummer))
            {
                $this->throwError('Ongeldig berichtnummer!');
                return false;
            }
            else
            {
                $this->toSock('LIST '.$iBerichtNummer);
                $this->sReturn = trim($this->stream_get_contents());
                //echo "\n".$this->sReturn."\n";

                if(substr($this->sReturn,0,3) != '+OK')
                {
                    return false;
                }
                $aReturnData = explode(' ',$this->sReturn);
                return array($aReturnData[1] => $aReturnData[2]);
            }
        }
        else
        {
			$aResult = [];
			
            $this->toSock('LIST');
            //echo "\ntoSock('LIST')\n";
            $this->sReturn = trim($this->stream_get_contents());
            if(substr($this->sReturn,0,3) != '+OK')
            {
                return false;
            }
            //ereg("^\+OK ([0-9]*) [aegms]{8} \(([0-9]*) [ceost]{6}\)(.*)\.$",$this->sReturn,$aMatch);
            /*preg_match('/^\+OK ([0-9]*) [aegms]{8} \(([0-9]*) [ceost]{6}\)(.*)\.$/',$this->sReturn,$aMatch);
            if(count($aMatch) > 1 && $aMatch[1] > 0)
            {
            $aResult[0] = array($aMatch[1] => $aMatch[2]);
            $aReturnData = explode("\n",$this->sReturn);
            foreach($aReturnData as $sRij)
            {
            $aRij = explode(' ',$sRij);
            if(count($aRij) == 2)
            {
            $aResult[$aRij[0]] = $aRij[1];
            }
            }
            }
            */
            if(substr(trim($this->sReturn),0,3) == '+OK')
            {
                $aReturnData = explode("\n",$this->sReturn);
                $c = count($aReturnData);
                for($i=1; $i<$c; $i++)
                {
                    $aRij = explode(' ',$aReturnData[$i]);
                    if(count($aRij) == 2)
                    {
                        //$aResult[$aRij[0]] = $aRij[1];
                        $aResult[] = $aRij[1];
                    }
                }

            }
            else
            {
                //$aResult = array(0=>array(0=>0));
            }
            return $aResult;
        }
    }
    public function retrieveMessage($iBerichtNummer, $bHeadersOnly = false)
    {
        $this->toSock('RETR '.$iBerichtNummer);
        $this->sReturn = trim($this->stream_get_contents(1024, ($bHeadersOnly ? "\n\n" : "")));
        if(substr($this->sReturn,0,3) != '+OK')
        {
            $aReturn = false;
        }
        else
        {
            //if(ereg("^\+OK ([0-9]*) [ceost]{6}\n(.*)\n\.",$this->sReturn,$aMatch))
            /*if(preg_match("/^\+OK ([0-9]*) [ceost]{6}\n(.*)\n\./",$this->sReturn,$aMatch))
            {
            //aMatch[1] => totale grootte bericht
            //aMatch[2] => totale bericht

            // is het bericht groter dan 500 bytes
            // Ja? dan is nog niet het hele bericht uitgelezen
            if($aMatch[1] > 502)
            {
            $aMatch[2] .= stream_get_contents($aMatch[1] - 512);
            }
            // Headers+bericht
            $aReturn['message'] = $aMatch[2];
            // headers worden afgesloten met een dubbele newline.
            $aReturnData = explode("\n\n",$aMatch[2]);
            $aReturn['headers'] = $aReturnData[0];
            $iHeaderLength = strlen($aReturnData[0]);
            // lengte die overblijft na het afknippen van de headers
            $aReturn['body'] = substr($aMatch[2],$iHeaderLength+2);
            }
            else
            {
            // ereg vond geen matchniet.
            $this->throwError('System: POP3-Server returned an invalid messageformat!');
            echo $this->sReturn;
            return false;
            }
            }*/
            
            //return $this->sReturn;
            
            $aReturn = [];
            $aReturnRaw = explode("\n\n",$this->sReturn, 2);

            $aReturn['header'] = [];
			
            $aHeaders = explode("\n",$aReturnRaw[0]);
			$iHeaderCount = count($aHeaders);
			for($i=1; $i<$iHeaderCount; $i++)
			{
				$aHeader = explode(": ",$aHeaders[$i], 2);
				if(count($aHeader) > 1)
				{
					if(isset($aReturn['header'][strtolower($aHeader[0])]))
					{
						$aReturn['header'][strtolower($aHeader[0])] .= "\n" . $aHeader[1];
					}
					else
					{
						$aReturn['header'][strtolower($aHeader[0])] = $aHeader[1];
					}
				}
				else
				{
					$aReturn['header'][strtolower($aHeader[0])] = '';
				}
			}
			
/*
Received: (qmail 16092 invoked by uid 110); 20 Dec 2014 12:28:54 +0100
Delivered-To: 616-bol@wikke.net
Received: (qmail 16056 invoked from network); 20 Dec 2014 12:28:53 +0100
Received: from pro-mail-smtp-001.bol.com (185.14.168.222)
  by vz04.stone-is.net with (DHE-RSA-AES256-SHA encrypted) SMTP; 20 Dec 2014 12:28:53 +0100
Received-SPF: pass (vz04.stone-is.net: SPF record at bol.com designates 185.14.168.222 as permitted sender)
Received: from pro-mail-smtp-001.bol.com (localhost [127.0.0.1])
	by pro-mail-smtp-001.bol.com (Postfix) with ESMTP id 65A07404C5
	for <bol@wikke.net>; Sat, 20 Dec 2014 12:28:53 +0100 (CET)
Received: from pro-eph-app-001.bolcom.net (pro-eph-app-001.bolcom.net [10.98.129.35])
	by pro-mail-smtp-001.bol.com (Postfix) with ESMTP id 64BF1404B3
	for <bol@wikke.net>; Sat, 20 Dec 2014 12:28:53 +0100 (CET)
Received: from pro-eph-app-001.bolcom.net (localhost.localdomain [127.0.0.1])
	by pro-eph-app-001.bolcom.net (Postfix) with ESMTP id 59D8428045
	for <bol@wikke.net>; Sat, 20 Dec 2014 12:28:53 +0100 (CET)
From: "bol.com" <automail@bol.com>
To: bol@wikke.net
Message-ID: <1504927778.643820.1419074933366.JavaMail.amr@127.0.0.1>
Subject: Bevestiging van je bestelling 8411587610
MIME-Version: 1.0
Content-Type: multipart/alternative; 
	boundary="----=_Part_643818_71451891.1419074933366"
Date: Sat, 20 Dec 2014 12:28:53 +0100 (CET)
*/
			if(!$bHeadersOnly){
				$aReturn['body'] = $aReturnRaw[1];
			}
			
            return $aReturn;
        }
    }
}
?> 