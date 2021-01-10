<?php
$readmail = new ReadGmailController();
$readmail->readmail();

class ReadGmailController
{
    public function readmail()
    {
        /* connect to gmail */
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = 'dsdvn2018@gmail.com';
        $password = 'Hjeu106@';

//        $username = 'bvhieu106@gmail.com';
//        $password = 'Trunghjeu106@!';

        /* try to connect */
        $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
        $nb_mail = imap_num_msg($inbox);
        $search = imap_search($inbox ,"ANSWERED");
        echo '<pre>';
        print_r($search);
        echo '</pre>';
        echo $nb_mail;
        die();
        $email_number = "843";

        /* grab emails */
        $overview = imap_fetch_overview($inbox,$email_number,0);
//        dd($overview);
//        $thread = imap_thread($inbox);
//        echo iconv_mime_decode($overview[0]->subject, 0, "UTF-8");
        global $charset,$htmlmsg,$plainmsg,$attachments;
        $this->getmsg($inbox, $email_number);
        imap_close($inbox);
        echo $htmlmsg;
		
       // ($charset,$htmlmsg,$plainmsg,$attachments);
    }

    function getmsg($mbox,$mid) {
        // input $mbox = IMAP stream, $mid = message id
        // output all the following:
        global $charset,$htmlmsg,$plainmsg,$attachments;
        $htmlmsg = $plainmsg = $charset = '';
        $attachments = array();

        // HEADER
        $h = imap_header($mbox,$mid);
		echo '<pre>';
	
		print_r($h);
        // add code here to get date, from, to, cc, subject...

        // BODY
        $s = imap_fetchstructure($mbox,$mid);
        if (!$s->parts) { // simple
            $this->getpart($mbox,$mid,$s,0);  // pass 0 as part-number
        }
        else {  // multipart: cycle through each part
            foreach ($s->parts as $partno0=>$p) {
                $this->getpart($mbox,$mid,$p,$partno0+1);
            }
        }
    }

    function getpart($mbox,$mid,$p,$partno) {
        // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
        global $htmlmsg,$plainmsg,$charset,$attachments;

        // DECODE DATA
        $data = ($partno)?
            imap_fetchbody($mbox,$mid,$partno):  // multipart
            imap_body($mbox,$mid);  // simple
        // Any part may be encoded, even plain text messages, so check everything.
        if ($p->encoding==4)
            $data = quoted_printable_decode($data);
        elseif ($p->encoding==3)
            $data = base64_decode($data);

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if (isset($p->parameters) && $p->parameters)
        {
            foreach ($p->parameters as $x)
            {
                $params[strtolower($x->attribute)] = $x->value;
            }
        }
        if (isset($p->dparameters) && $p->dparameters)
        {
            foreach ($p->dparameters as $x) {
                $params[strtolower($x->attribute)] = $x->value;
            }
        }

        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if (isset($params['filename']) || isset($params['name'])) {
            // filename may be given as 'Filename' or 'Name' or both
            $filename = ($params['filename'])? $params['filename'] : $params['name'];
            // filename may be encoded, so see imap_mime_header_decode()
            $attachments[$filename] = $data;  // this is a problem if two files have same name
        }

        // TEXT
        if ($p->type==0 && $data) {
            // Messages may be split in different parts because of inline attachments,
            // so append parts together with blank row.
                if (strtolower($p->subtype)=='plain') {
                    $plainmsg .= trim($data) ."\n\n";
                }
                else {
                    $htmlmsg .= $data ."<br><br>";
                }
            $charset = $params['charset'];  // assume all parts are same charset
        }

        // EMBEDDED MESSAGE
        // Many bounce notifications embed the original message as type 2,
        // but AOL uses type 1 (multipart), which is not handled here.
        // There are no PHP functions to parse embedded messages,
        // so this just appends the raw source to the main message.
        elseif ($p->type==2 && $data) {
            $plainmsg .= $data."\n\n";
         }

        // SUBPART RECURSION
        if (isset($p->parts) && $p->parts) {
            foreach ($p->parts as $partno0=>$p2)
                getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
        }
    }
}