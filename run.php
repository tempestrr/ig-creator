<?php  
include 'curl.php';
define('URL', 'https://www.instagram.com');
define('API', 'https://i.instagram.com');


function instagram_creator() {
	$proxy = null;
	$fake_name = curl('https://fakenametool.net/generator/random/id_ID/indonesia');
	preg_match_all('/<td>(.*?)<\/td>/s', $fake_name, $result);
	$name = $result[1][0];
	$domain = ['carpin.org', 'novaemail.com'];
	$rand = array_rand($domain);
	$email = str_replace(' ', '', strtolower($name)).number(4).'@'.$domain[$rand];
	$username = explode('@', $email);
	$client = random(13).'-'.random(14);

	$headers = [
		'Authority: www.instagram.com',
		'Accept: */*',
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36 OPR/70.0.3728.119',
		'Sec-Fetch-Site: same-origin',
		'Sec-Fetch-Mode: cors',
		'Sec-Fetch-Dest: empty'
	];


	$instagram = curl(URL.'/accounts/signup/email', null, $headers, $proxy);
	$cookies = getcookies($instagram);

	if ($instagram) {

		$headers = [
			'Authority: api.mail.tm',
			'Accept: application/json, text/plain, */*',
			'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Mobile Safari/537.36',
			'Content-Type: application/json;charset=UTF-8'
		];

		echo "\n[!] Try to generate email \n";
		$temp = curl('https://api.mail.tm/accounts', '{"address":"'.$email.'","password":"yudhagans"}', $headers);
		preg_match('/"id":"(.*?)"/s', $temp, $id);
		$token_page = curl('https://api.mail.tm/authentication_token', '{"@context":"/contexts/Account","@id":"/accounts/'.$id[1].'","@type":"Account","id":"'.$id[1].'","address":"'.$email.'","quota":0,"used":0,"is_disabled":false,"created_at":null,"updated_at":null,"password":"yudhagans"}', $headers);
		preg_match('/"token":"(.*?)"/s', $token_page, $token);

		if ($token[1] != "") {

			echo "[!] Success generate email ".$email."\n";
			echo "[!] Try to register in instagram.com\n";

			$headerx = [
				'Authority: www.instagram.com',
				'Content-Type: application/x-www-form-urlencoded',
				'Accept: */*',
				'X-Csrftoken: '.$cookies['csrftoken'],
				'Accept-Language: en-GB,en-US;q=0.9,en;q=0.8',
				'Cookie: ig_did='.$cookies['ig_did'].'; mid='.$cookies['mid'].'; fbm_124024574287414=base_domain=.instagram.com; shbid=2402; shbts=1598540440.4550562; rur=ATN; csrftoken='.$cookies['csrftoken'].'; urlgen="{\"36.73.116.228\": 7713\054 \"180.246.195.236\": 7713}:1kBU8q:HkmrV3FP6760LvrkqAhgVgBosbA"'
			];

			$web_create_ajax = curl(URL.'/accounts/web_create_ajax/attempt/', 'email='.$email.'&enc_password=%23PWD_INSTAGRAM_BROWSER%3A10%3A1598581765%3AAUpQAC6aiwPMxYVs7yX4BLCwwnIZaPBmrH0wzngGkwXdcnpgWXWi5%2BGggsy38YJ9tq4tGmJHYwtvQKNmNxiL3%2B%2FBmEEPCOLA9TYRV0EEkhE4zDoSEPfdb3xLofKr7RXneCcunOmQI8er3nDQAQ%3D%3D&username='.$username[0].'&first_name='.$name.'&client_id='.$client.'&seamless_login_enabled=1&opt_into_one_tap=false', $headerx, $proxy);

			if (stripos($web_create_ajax, '"status": "ok"')) {
				$check_age = curl(URL.'/web/consent/check_age_eligibility/', 'day=13&month=7&year=1982', $headerx, $proxy);

				if (stripos($check_age, '"eligible_to_register": true')) {
					$web_create =  curl(URL.'/accounts/web_create_ajax/attempt/', 'email='.$email.'&enc_password=%23PWD_INSTAGRAM_BROWSER%3A10%3A1598582775%3AAUpQAIreIv6mvhh8OV2rhxoR5JXtHsEqnxQMEl2A0sJPevtrWOgSevl%2BMeg04J8CvHnWE5koajKbStcpbNd2hNF%2FSBfU%2FOy43scFHE2GCfDjmOauFGAThvHK%2F83p8EKzn3A88gIj%2BLa3FVCzkA%3D%3D&username='.$username[0].'&first_name='.$name.'&month=7&day=13&year=1982&client_id='.$client.'&seamless_login_enabled=1', $headerx, $proxy);

					if (stripos($web_create, '"status": "ok"')) {
						$send_verif = curl(API.'/api/v1/accounts/send_verify_email/', 'device_id='.$client.'&email='.$email, $headerx, $proxy);

						echo "[!] Send verification code : ";
						sleep(1);
						if (stripos($send_verif, '"email_sent": true')) {
							echo "Ok\n";
							echo "[!] Delay 15 seconds to get verification code\n";
							sleep(15);

							$headers = [
								'Authority: api.mail.tm',
								'Accept: application/json, text/plain, */*',
								'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Mobile Safari/537.36',
								'Content-Type: application/json;charset=UTF-8',
								'Authorization: Bearer '.$token[1]
							];

							$msg = curl('https://api.mail.tm/messages', null, $headers);

							if (stripos($msg, '"name":"Instagram"')) {
								echo "[*] Try to get verification code : ";
								preg_match('/"subject":"(.*?) is your Instagram code"/s', $msg, $code);
								echo $code[1]."\n";
								$check_code = curl(API.'/api/v1/accounts/check_confirmation_code/', 'code='.$code[1].'&device_id='.$client.'&email='.$email, $headerx, $proxy);
								preg_match('/"signup_code": "(.*?)",/s', $check_code, $signup_code);

								echo $final = curl(URL.'/accounts/web_create_ajax/', 'email='.$email.'&enc_password=%23PWD_INSTAGRAM_BROWSER%3A10%3A1598583059%3AAUpQAP24dbBsHZ7LBC%2FAimdq4X9CAmbG5XvqUz%2B9e08WnFRFIy%2F5mJ9qeztYULkwsXBg79JLGXGrJCgOP4CWN%2B8vS4Z2Jzy8Lzvta1BCd9mLQkh9s82C77qoJVlKqkJNVzQ2rGwf5FduR3lsCA%3D%3D&username='.$username[0].'&first_name='.$name.'&month=7&day=13&year=1982&client_id='.$client.'&seamless_login_enabled=1&tos_version=row&force_sign_up_code='.$signup_code[1], $headerx, $proxy);

								
							} else {
								echo "Email not found\n";
							}

						} else {
							echo "Failed\n";
						}

					}

				} else {
					echo "[!] Something went wrong\n";
				}

			} else {
				echo "[!] Something went wrong\n";
			}

		} else {
			echo "[!] Token not found\n";
		}
	} else {
		echo "[!] Socks die | ".$proxy."\n";
	}


	
}


echo "How many accout u wanna to create : ";
$banyak = trim(fgets(STDIN));

for ($i = 0; $i < $banyak ; $i++) {
	instagram_creator();
}
$file = file_get_contents($namefile) or die ("File Not Found\n");
$socks = explode("\r\n",$file);
$total = count($socks);
echo "Total Socks: ".$total."\n";

foreach ($socks as $value) {
	instagram_creator($value);
}


















?>
