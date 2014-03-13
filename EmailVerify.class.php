<?php
##############################################################################################################
#
# Basic email address verification class
# This class can determine if an email address is correctly formatted and if the domain for the email address
#	exists, but cannot determine if the actual email address itself exists
#
# Author: Todd D. Webb
# Contact: DukeOfMarshall@gmail.com
#
# Sites
#	http://www.dukeofmarshall.com
#	http://blog.dukeofmarshall.com
#	http://www.techwerks.tv
#	http://www.soundbytes.biz
#
##############################################################################################################

# What to do if the class is being called directly and not being included in a script via PHP
# This allows the class/script to be called via other methods like JavaScript
if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])){
	$return_array = array();
	
	if($_GET['address_to_verify'] == '' || !isset($_GET['address_to_verify'])){
		$return_array['error'] 				= 1;
		$return_array['message'] 			= 'No email address was submitted for verification';
		$return_array['domain_verified'] 	= 0;
		$return_array['format_verified'] 	= 0;
	}else{
		$verify = new EmailVerify();
		
		$format_check = $verify->verify_formatting($_GET['address_to_verify'], $_GET['verbose']);
		
		if($format_check === TRUE){
			$return_array['format_verified'] 	= 1;
			
			if($verify->verify_domain($_GET['address_to_verify'])){
				$return_array['error'] 				= 0;
				$return_array['domain_verified'] 	= 1;
				$return_array['message'] 			= 'Formatting and domain have been verified';
			}else{
				$return_array['error'] 				= 1;
				$return_array['domain_verified'] 	= 0;
				$return_array['message'] 			= 'Formatting was verified, but verification of the domain has failed';
			}
		}else{
			$return_array['error'] 				= 1;
			$return_array['domain_verified'] 	= 0;
			$return_array['format_verified'] 	= 0;
			$return_array['message'] 			= $format_check ?: 'Email was not formatted correctly';
		}
	}
	
	echo json_encode($return_array);
	
	exit();
}

class EmailVerify {
	# Comprehensive list of domain name extensions from http://data.iana.org/TLD/tlds-alpha-by-domain.txt
	# http://www.icann.org/en/resources/registries/tlds
	#
	# Does not include domains with non-latin characters
	public $list_domain_extensions = array(
										'ac',
										'academy',
										'actor',
										'ad',
										'ae',
										'aero',
										'af',
										'ag',
										'agency',
										'ai',
										'al',
										'am',
										'an',
										'ao',
										'aq',
										'ar',
										'arpa',
										'as',
										'asia',
										'at',
										'au',
										'aw',
										'ax',
										'az',
										'ba',
										'bar',
										'bargains',
										'bb',
										'bd',
										'be',
										'berlin',
										'best',
										'bf',
										'bg',
										'bh',
										'bi',
										'bid',
										'bike',
										'biz',
										'bj',
										'blue',
										'bm',
										'bn',
										'bo',
										'boutique',
										'br',
										'bs',
										'bt',
										'build',
										'builders',
										'buzz',
										'bv',
										'bw',
										'by',
										'bz',
										'ca',
										'cab',
										'camera',
										'camp',
										'cards',
										'careers',
										'cat',
										'catering',
										'cc',
										'cd',
										'center',
										'ceo',
										'cf',
										'cg',
										'ch',
										'cheap',
										'christmas',
										'ci',
										'ck',
										'cl',
										'cleaning',
										'clothing',
										'club',
										'cm',
										'cn',
										'co',
										'codes',
										'coffee',
										'com',
										'community',
										'company',
										'computer',
										'condos',
										'construction',
										'contractors',
										'cool',
										'coop',
										'cr',
										'cruises',
										'cu',
										'cv',
										'cw',
										'cx',
										'cy',
										'cz',
										'dance',
										'dating',
										'de',
										'democrat',
										'diamonds',
										'directory',
										'dj',
										'dk',
										'dm',
										'do',
										'domains',
										'dz',
										'ec',
										'edu',
										'education',
										'ee',
										'eg',
										'email',
										'enterprises',
										'equipment',
										'er',
										'es',
										'estate',
										'et',
										'eu',
										'events',
										'expert',
										'exposed',
										'farm',
										'fi',
										'fish',
										'fj',
										'fk',
										'flights',
										'florist',
										'fm',
										'fo',
										'foundation',
										'fr',
										'futbol',
										'ga',
										'gallery',
										'gb',
										'gd',
										'ge',
										'gf',
										'gg',
										'gh',
										'gi',
										'gift',
										'gl',
										'glass',
										'gm',
										'gn',
										'gov',
										'gp',
										'gq',
										'gr',
										'graphics',
										'gs',
										'gt',
										'gu',
										'guitars',
										'guru',
										'gw',
										'gy',
										'hk',
										'hm',
										'hn',
										'holdings',
										'holiday',
										'house',
										'hr',
										'ht',
										'hu',
										'id',
										'ie',
										'il',
										'im',
										'immobilien',
										'in',
										'industries',
										'info',
										'institute',
										'int',
										'international',
										'io',
										'iq',
										'ir',
										'is',
										'it',
										'je',
										'jm',
										'jo',
										'jobs',
										'jp',
										'kaufen',
										'ke',
										'kg',
										'kh',
										'ki',
										'kim',
										'kitchen',
										'kiwi',
										'km',
										'kn',
										'koeln',
										'kp',
										'kr',
										'kred',
										'kw',
										'ky',
										'kz',
										'la',
										'land',
										'lb',
										'lc',
										'li',
										'lighting',
										'limo',
										'link',
										'lk',
										'lr',
										'ls',
										'lt',
										'lu',
										'luxury',
										'lv',
										'ly',
										'ma',
										'maison',
										'management',
										'mango',
										'marketing',
										'mc',
										'md',
										'me',
										'menu',
										'mg',
										'mh',
										'mil',
										'mk',
										'ml',
										'mm',
										'mn',
										'mo',
										'mobi',
										'moda',
										'monash',
										'mp',
										'mq',
										'mr',
										'ms',
										'mt',
										'mu',
										'museum',
										'mv',
										'mw',
										'mx',
										'my',
										'mz',
										'na',
										'nagoya',
										'name',
										'nc',
										'ne',
										'net',
										'neustar',
										'nf',
										'ng',
										'ni',
										'ninja',
										'nl',
										'no',
										'np',
										'nr',
										'nu',
										'nz',
										'okinawa',
										'om',
										'onl',
										'org',
										'pa',
										'partners',
										'parts',
										'pe',
										'pf',
										'pg',
										'ph',
										'photo',
										'photography',
										'photos',
										'pics',
										'pink',
										'pk',
										'pl',
										'plumbing',
										'pm',
										'pn',
										'post',
										'pr',
										'pro',
										'productions',
										'properties',
										'ps',
										'pt',
										'pub',
										'pw',
										'py',
										'qa',
										'qpon',
										're',
										'recipes',
										'red',
										'rentals',
										'repair',
										'report',
										'reviews',
										'rich',
										'ro',
										'rs',
										'ru',
										'ruhr',
										'rw',
										'sa',
										'sb',
										'sc',
										'sd',
										'se',
										'sexy',
										'sg',
										'sh',
										'shiksha',
										'shoes',
										'si',
										'singles',
										'sj',
										'sk',
										'sl',
										'sm',
										'sn',
										'so',
										'social',
										'solar',
										'solutions',
										'sr',
										'st',
										'su',
										'supplies',
										'supply',
										'support',
										'sv',
										'sx',
										'sy',
										'systems',
										'sz',
										'tattoo',
										'tc',
										'td',
										'technology',
										'tel',
										'tf',
										'tg',
										'th',
										'tienda',
										'tips',
										'tj',
										'tk',
										'tl',
										'tm',
										'tn',
										'to',
										'today',
										'tokyo',
										'tools',
										'tp',
										'tr',
										'training',
										'travel',
										'tt',
										'tv',
										'tw',
										'tz',
										'ua',
										'ug',
										'uk',
										'uno',
										'us',
										'uy',
										'uz',
										'va',
										'vacations',
										'vc',
										've',
										'ventures',
										'vg',
										'vi',
										'viajes',
										'villas',
										'vision',
										'vn',
										'vote',
										'voting',
										'voto',
										'voyage',
										'vu',
										'wang',
										'watch',
										'wed',
										'wf',
										'wien',
										'wiki',
										'works',
										'ws',
										'xxx',
										'xyz',
										'ye',
										'yt',
										'za',
										'zm',
										'zone',
										'zw'
	);
	
	public function __construct(){
	}
	
	# Verify the DNS records according to the domain name given in the email address
	public function verify_domain($address_to_verify, $verbose=FALSE){
		$record = 'MX'; # <-- Can be changed to check for other records like A records or CNAME records as well
		list($user, $domain) = explode('@', $address_to_verify);
		return checkdnsrr($domain, $record);
	}
	
	# Verify that the email address is formatted as an email address should be
	public function verify_formatting($address_to_verify, $verbose=FALSE){
		
		# Check to make sure the @ symbol is included
		if(strstr($address_to_verify, "@") == FALSE){
			if($verbose){
				return 'Ampersand not present.';
			}else{
				return false;
			}
		}else{
			
			# Bust up the address so that we have the name and the domain name
			list($user, $domain) = explode('@', $address_to_verify);
			
			# Verify the domain name has a period like all good domain names should
			if(strstr($domain, '.') == FALSE){
				if($verbose){
					return 'Period not present.';
				}else{
					return false;
				}
			}else{
				
				# Bust up the domain name
				$domain_check = explode(".", $domain);
				$domain_extension = end($domain_check);
				
				if(strlen($domain_extension) < 2){
					if($verbose){
						return 'Domain name extension is too short.';
					}else{
						return false;
					}
				}else{
					if(!in_array($domain_extension, $this->list_domain_extensions)){
						if($verbose){
							return 'Domain name extension could not be verified.';
						}else{
							return false;
						}
					}else{
						return true;
					}
				}
			}
		}
	}

	# Take the code from an HTML email and convert it to plain text
	# This is commonly used when sending HTML emails as a backup for email clients who can only view, or who choose to only view, 
	#	plain text emails
	public function convert_html_to_plain_txt($content, $remove_links=FALSE){
		# Replace HTML line breaks with text line breaks
		$plain_text = str_ireplace(array("<br>","<br />"), "\n\r", $content);
		
		# Remove the content between the tags that wouldn't normally get removed with the strip_tags function
		$plain_text = preg_replace(array('@<head[^>]*?>.*?</head>@siu',
							            '@<style[^>]*?>.*?</style>@siu',
							            '@<script[^>]*?.*?</script>@siu',
							            '@<noscript[^>]*?.*?</noscript>@siu',
							        ), "", $plain_text); # Remove everything from between the tags that doesn't get removed with strip_tags function
		
		# If the user has chosen to preserve the addresses from links
		if(!$remove_links){
			$plain_text = strip_tags(preg_replace('/<a href="(.*)">/', ' $1 ', $plain_text));
		}
		
		# Remove HTML spaces
		$plain_text = str_replace("&nbsp;", "", $plain_text);
		
		# Replace multiple line breaks with a single line break
		$plain_text = preg_replace("/(\s){3,}/","\r\n\r\n",trim($plain_text));
		
		return $plain_text;
	}
}
?>
