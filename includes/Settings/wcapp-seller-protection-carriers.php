<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Seller Protection shipment Carriers.
 */
$carriers = array(
	'Global' => array(
		array('Aramex','ARAMEX','GLOBAL'),
		array('B2C Europe','B_TWO_C_EUROPE','GLOBAL'),
		array('CJ Logistics','CJ_LOGISTICS','GLOBAL'),
		array('Correos Express','CORREOS_EXPRESS','GLOBAL'),
		array('DHL Active Tracing','DHL_ACTIVE_TRACING','GLOBAL'),
		array('DHL Benelux','DHL_BENELUX','GLOBAL'),
		array('DHL ecCommerce US','DHL_GLOBAL_MAIL','GLOBAL'),
		array('DHL eCommerce Asia','DHL_GLOBAL_MAIL_ASIA','GLOBAL'),
		array('DHL Express','DHL','GLOBAL'),
		array('DHL Global eCommerce','DHL_GLOBAL_ECOMMERCE','GLOBAL'),
		array('DHL Packet','DHL_PACKET','GLOBAL'),
		array('DPD Global','DPD','GLOBAL'),
		array('DPD Local','DPD_LOCAL','GLOBAL'),
		array('DPD Local Reference','DPD_LOCAL_REF','GLOBAL'),
		array('DPE Express','DPE_EXPRESS','GLOBAL'),
		array('DPEX Hong Kong','DPEX','GLOBAL'),
		array('DTDC Express Global','DTDC_EXPRESS','GLOBAL'),
		array('EShopWorld','ESHOPWORLD','GLOBAL'),
		array('FedEx','FEDEX','GLOBAL'),
		array('FLYT Express','FLYT_EXPRESS','GLOBAL'),
		array('GLS','GLS','GLOBAL'),
		array('IMX France','IMX','GLOBAL'),
		array('International SEUR','INT_SUER','GLOBAL'),
		array('Landmark Global','LANDMARK_GLOBAL','GLOBAL'),
		array('Matkahuoloto','MATKAHUOLTO','GLOBAL'),
		array('Omni Parcel','OMNIPARCEL','GLOBAL'),
		array('One World','ONE_WORLD','GLOBAL'),
		array('Other','OTHER','GLOBAL'),
		array('Posti','POSTI','GLOBAL'),
		array('Raben Group','RABEN_GROUP','GLOBAL'),
		array('SF EXPRESS','SF_EXPRESS','GLOBAL'),
		array('SkyNet Worldwide Express','SKYNET_Worldwide','GLOBAL'),
		array('Spreadel','SPREADEL','GLOBAL'),
		array('TNT Global','TNT','GLOBAL'),
		array('UPS','UPS','GLOBAL'),
		array('UPS Mail Innovations','UPS_MI','GLOBAL'),
		array('WebInterpret','WEBINTERPRET','GLOBAL')
	),
	'Other' => array(
		array('Other','OTHER','GLOBAL')
	),
	'Antigua and Barbuda' => array(
		array('Correos Antigua and Barbuda','CORREOS_AG','AG')
	),
	'Argentina' => array(
		array('Emirates Post','EMIRATES_POST','AR'),
		array('OCA Argentina','OCA_AR','AR')
	),
	'Australia' => array(
		array('Adsone','ADSONE','AU'),
		array('Australia Post','AUSTRALIA_POST','AU'),
		array('Australia Toll','TOLL_AU','AU'),
		array('Bonds Couriers','BONDS_COURIERS','AU'),
		array('Couriers Please','COURIERS_PLEASE','AU'),
		array('DHL Australia','DHL_AU','AU'),
		array('DTDC Australia','DTDC_AU','AU'),
		array('Fastway Australia','FASTWAY_AU','AU'),
		array('Hunter Express','HUNTER_EXPRESS','AU'),
		array('Sendle','SENDLE','AU'),
		array('Star Track','STARTRACK','AU'),
		array('Star Track Express','STARTRACK_EXPRESS','AU'),
		array('TNT Australia','TNT_AU','AU'),
		array('Toll','TOLL','AU'),
		array('UBI Logistics','UBI_LOGISTICS','AU')
	),
	'Austria' => array(
		array('Austrian Post Express','AUSTRIAN_POST_EXPRESS','AT'),
		array('Austrian Post Registered','AUSTRIAN_POST','AT'),
		array('DHL Austria','DHL_AT','AT')
	),
	'Belgium' => array(
		array('bpost','BPOST','BE'),
		array('bpost International','BPOST_INT','BE'),
		array('Mondial Belgium','MONDIAL_BE','BE'),
		array('TaxiPost','TAXIPOST','BE')
	),
	'Brazil' => array(
		array('Correos Brazil','CORREOS_BR','BR'),
		array('Directlog','DIRECTLOG_BR','BR')
	),
	'Bulgaria' => array(
		array('Bulgarian Post','BULGARIAN_POST','BG')
	),
	'Canada' => array(
		array('Canada Post','CANADA_POST','CA'),
		array('Canpar','CANPAR','CA'),
		array('Greyhound','GREYHOUND','CA'),
		array('Loomis','LOOMIS','CA'),
		array('Purolator','PUROLATOR','CA')
	),
	'Chile' => array(
		array('Correos Chile','CORREOS_CL','CL')
	),
	'China' => array(
		array('4PX Express','FOUR_PX_EXPRESS','CN'),
		array('AUPOST CHINA','AUPOST_CN','CN'),
		array('BQC Express','BQC_EXPRESS','CN'),
		array('Buylogic','BUYLOGIC','CN'),
		array('China Post','CHINA_POST','CN'),
		array('CN Exps','CNEXPS','CN'),
		array('EC China','EC_CN','CN'),
		array('EFS','EFS','CN'),
		array('EMPS China','EMPS_CN','CN'),
		array('EMS China','EMS_CN','CN'),
		array('Huahan Express','HUAHAN_EXPRESS','CN'),
		array('SFC Express','SFC_EXPRESS','CN'),
		array('TNT China','TNT_CN','CN'),
		array('WinIt','WINIT','CN'),
		array('Yanwen','YANWEN_CN','CN')
	),
	'Costa Rica' => array(
		array('Correos De Costa Rica','CORREOS_CR','CR')
	),
	'Croatia' => array(
		array('Hrvatska','HRVATSKA_HR','HR')
	),
	'Cyprus' => array(
		array('Cyprus Post','CYPRUS_POST_CYP','CY')
	),
	'Czech Republic' => array(
		array('Ceska','CESKA_CZ','CZ'),
		array('GLS Czech Republic','GLS_CZ','CZ')
	),
	'France' => array(
		array('BERT TRANSPORT','BERT','FR'),
		array('Chronopost France','CHRONOPOST_FR','FR'),
		array('Coliposte','COLIPOSTE','FR'),
		array('Colis France','COLIS','FR'),
		array('DHL France','DHL_FR','FR'),
		array('DPD France','DPD_FR','FR'),
		array('GEODIS - Distribution & Express','GEODIS','FR'),
		array('GLS France','GLS_FR','FR'),
		array('LA Poste','LAPOSTE','FR'),
		array('Mondial Relay','MONDIAL','FR'),
		array('Relais Colis','RELAIS_COLIS_FR','FR'),
		array('Teliway','TELIWAY','FR'),
		array('TNT France','TNT_FR','FR')
	),
	'Germany' => array(
		array('Asendia Germany','ASENDIA_DE','DE'),
		array('Deltec Germany','DELTEC_DE','DE'),
		array('Deutsche','DEUTSCHE_DE','DE'),
		array('DHL Deutsche Post','DHL_DEUTSCHE_POST','DE'),
		array('DPD Germany','DPD_DE','DE'),
		array('GLS Germany','GLS_DE','DE'),
		array('Hermes Germany','HERMES_DE','DE'),
		array('TNT Germany','TNT_DE','DE')
	),
	'Greece' => array(
		array('ELTA Greece','ELTA_GR','GR'),
		array('Geniki Greece','GENIKI_GR','GR'),
		array('GRC Greece','ACS_GR','GR')
	),
	'Hong Kong' => array(
		array('Asendia Hong Kong','ASENDIA_HK','HK'),
		array('DHL Hong Kong','DHL_HK','HK'),
		array('DPD Hong Kong','DPD_HK','HK'),
		array('Hong Kong Post','HK_POST','HK'),
		array('Kerry Express Hong Kong','KERRY_EXPRESS_HK','HK'),
		array('Logistics Worldwide Hong Kong','LOGISTICSWORLDWIDE_HK','HK'),
		array('Quantium','QUANTIUM','HK'),
		array('Seko Logistics','SEKOLOGISTICS','HK'),
		array('TA-Q-BIN Parcel Hong Kong','TAQBIN_HK','HK')
	),
	'Hungary' => array(
		array('Magyar','MAGYAR_HU','HU')
	),
	'Iceland' => array(
		array('Postur','POSTUR_IS','IS')
	),
	'India' => array(
		array('Bluedart','BLUEDART','IN'),
		array('Delhivery','DELHIVERY_IN','IN'),
		array('DotZot','DOTZOT','IN'),
		array('DTDC India','DTDC_IN','IN'),
		array('Ekart','EKART','IN'),
		array('India Post','INDIA_POST','IN'),
		array('Professional Couriers','PROFESSIONAL_COURIERS','IN'),
		array('Red Express','REDEXPRESS','IN'),
		array('Swift Air','SWIFTAIR','IN'),
		array('Xpress Bees','XPRESSBEES','IN')
	),
	'Indonesia' => array(
		array('First Logistics','FIRST_LOGISITCS','ID'),
		array('JNE Indonesia','JNE_IDN','ID'),
		array('Lion Parcel','LION_PARCEL','ID'),
		array('Ninjavan Indonesia','NINJAVAN_ID','ID'),
		array('Pandu Logistics','PANDU','ID'),
		array('Pos Indonesia Domestic','POS_ID','ID'),
		array('Pos Indonesia International','POS_INT','ID'),
		array('RPX Indonesia','RPX_ID','ID'),
		array('RPX International','RPX','ID'),
		array('Tiki','TIKI_ID','ID'),
		array('Wahana','WAHANA_ID','ID')
	),
	'Ireland' => array(
		array('AN POST Ireland','AN_POST','IE'),
		array('DPD Ireland','DPD_IR','IE'),
		array('Masterlink','MASTERLINK','IE'),
		array('TPG','TPG','IE'),
		array('Wiseloads','WISELOADS','IE')
	),
	'Israel' => array(
		array('Israel Post','ISRAEL_POST','IL')
	),
	'Italy' => array(
		array('BRT Bartolini','BRT_IT','IT'),
		array('DHL Italy','DHL_IT','IT'),
		array('DMM Network','DMM_NETWORK','IT'),
		array('FERCAM Logistics & Transport','FERCAM_IT','IT'),
		array('GLS Italy','GLS_IT','IT'),
		array('Hermes Italy','HERMES_IT','IT'),
		array('Poste Italiane','POSTE_ITALIANE','IT'),
		array('Register Mail IT','REGISTER_MAIL_IT','IT'),
		array('SDA Italy','SDA_IT','IT'),
		array('SGT Corriere Espresso','SGT_IT','IT'),
		array('TNT Click Italy','TNT_CLICK_IT','IT'),
		array('TNT Italy','TNT_IT','IT')
	),
	'Japan' => array(
		array('DHL Japan','DHL_JP','JP'),
		array('Japan Post','JP_POST','JP'),
		array('Japan Post','JAPAN_POST','JP'),
		array('Pocztex','POCZTEX','JP'),
		array('Sagawa','SAGAWA','JP'),
		array('Sagawa','SAGAWA_JP','JP'),
		array('TNT Japan','TNT_JP','JP'),
		array('Yamato Japan','YAMATO','JP')
	),
	'Korea' => array(
		array('Ecargo','ECARGO','KR'),
		array('eParcel Korea','EPARCEL_KR','KR'),
		array('Korea Post','KOREA_POST','KR'),
		array('Korea Post','KOR_KOREA_POST','KR'),
		array('Korea Thai CJ','CJ_KR','KR'),
		array('Logistics Worldwide Korea','LOGISTICSWORLDWIDE_KR','KR'),
		array('Pantos','PANTOS','KR'),
		array('Rincos','RINCOS','KR'),
		array('Rocket Parcel International','ROCKET_PARCEL','KR'),
		array('SRE Korea','SRE_KOREA','KR')
	),
	'Lithuania' => array(
		array('Lietuvos Pastas','LIETUVOS_LT','LT')
	),
	'Malaysia' => array(
		array('Airpak','AIRPAK_MY','MY'),
		array('CityLink Malaysia','CITYLINK_MY','MY'),
		array('CJ Malaysia','CJ_MY','MY'),
		array('CJ Malaysia International','CJ_INT_MY','MY'),
		array('Cuckoo Express','CUCKOOEXPRESS','MY'),
		array('Jet Ship Malaysia','JETSHIP_MY','MY'),
		array('Kangaroo Express','KANGAROO_MY','MY'),
		array('Logistics Worldwide Malaysia','LOGISTICSWORLDWIDE_MY','MY'),
		array('Malaysia Post EMS / Pos Laju','MALAYSIA_POST','MY'),
		array('Nationwide','NATIONWIDE','MY'),
		array('Ninjavan Malaysia','NINJAVAN_MY','MY'),
		array('Skynet Malaysia','SKYNET_MY','MY'),
		array('TA-Q-BIN Parcel Malaysia','TAQBIN_MY','MY')
	),
	'Mexico' => array(
		array('Correos De Mexico','CORREOS_MX','MX'),
		array('Estafeta','ESTAFETA','MX'),
		array('Mexico Aeroflash','AEROFLASH','MX'),
		array('Mexico Redpack','REDPACK','MX'),
		array('Mexico Senda Express','SENDA_MX','MX')
	),
	'Netherlands' => array(
		array('DHL Netherlands','DHL_NL','NL'),
		array('DHL Netherlands','DHL_NL','NL'),
		array('DHL Parcel Netherlands','DHL_PARCEL_NL','NL'),
		array('GLS Netherlands','GLS_NL','NL'),
		array('Kiala','KIALA','NL'),
		array('PostNL','POSTNL','NL'),
		array('PostNl International','POSTNL_INT','NL'),
		array('PostNL International 3S','POSTNL_INT_3_S','NL'),
		array('TNT Netherlands','TNT_NL','NL'),
		array('Transmission Netherlands','TRANSMISSION','NL')
	),
	'New Zealand' => array(
		array('Courier Post','COURIER_POST','NZ'),
		array('Fastway New Zealand','FASTWAY_NZ','NZ'),
		array('New Zealand Post','NZ_POST','NZ'),
		array('Toll IPEC','TOLL_IPEC','NZ')
	),
	'Nigeria' => array(
		array('Courier Plus','COURIERPLUS','NG'),
		array('NiPost','NIPOST_NG','NG')
	),
	'Norway' => array(
		array('Posten Norge','POSTEN_NORGE','NO')
	),
	'Philippines' => array(
		array('2GO','TWO_GO','PH'),
		array('Air 21','AIR_21','PH'),
		array('Airspeed','AIRSPEED','PH'),
		array('Jam Express','JAMEXPRESS_PH','PH'),
		array('LBC Express','LBC_PH','PH'),
		array('Ninjavan Philippines','NINJAVAN_PH','PH'),
		array('RAF Philippines','RAF_PH','PH'),
		array('Xend Express','XEND_EXPRESS_PH','PH')
	),
	'Poland' => array(
		array('DHL Poland','DHL_PL','PL'),
		array('DPD Poland','DPD_PL','PL'),
		array('InPost Paczkomaty','INPOST_PACZKOMATY','PL'),
		array('Poczta Polska','POCZTA_POLSKA','PL'),
		array('Siodemka','SIODEMKA','PL'),
		array('TNT Poland','TNT_PL','PL')
	),
	'Portugal' => array(
		array('Adicional Logistics','ADICIONAL_PT','PT'),
		array('Chronopost Portugal','CHRONOPOST_PT','PT'),
		array('Portugal PTT','CTT_PT','PT'),
		array('Portugal Seur','SEUR_PT','PT')
	),
	'Portugal' => array(
		array('DPD Romania','DPD_RO','RO'),
		array('Postaromana','POSTA_RO','RO')
	),
	'Russia' => array(
		array('DPD Russia','DPD_RU','RU'),
		array('Russian Post','RUSSIAN_POST','RU')
	),
	'Saudi Arabia' => array(
		array('Dawn Wing','DAWN_WING','SA'),
		array('Ram','RAM','SA'),
		array('The Courier Guy','THE_COURIER_GUY','SA')
	),
	'Serbia' => array(
		array('Serbia Post','POST_SERBIA_CS','CS')
	),
	'Singapore' => array(
		array('DHL Singapore','DHL_SG','SG'),
		array('JetShip Singapore','JETSHIP_SG','SG'),
		array('Ninjavan Singapore','NINJAVAN_SG','SG'),
		array('Parcel Post','PARCELPOST_SG','SG'),
		array('Singapore Post','SINGPOST','SG'),
		array('TA-Q-BIN Parcel Singapore','TAQBIN_SG','SG')
	),
	'South Africa' => array(
		array('Fastway South Africa','FASTWAY_ZA','ZA')
	),
	'Spain' => array(
		array('ASM','ASM_ES','ES'),
		array('CBL Logistics','CBL_LOGISTICA','ES'),
		array('Correos De Spain','CORREOS_ES','ES'),
		array('DHL Spain','DHL_ES','ES'),
		array('DHL Parcel Spain','DHL_PARCEL_ES','ES'),
		array('GLS Spain','GLS_ES','ES'),
		array('International Suer','INT_SEUR','ES'),
		array('ITIS','ITIS','ES'),
		array('Nacex Spain','NACEX_ES','ES'),
		array('Redur Spain','REDUR_ES','ES'),
		array('Spanish Seur','SEUR_ES','ES'),
		array('TNT Spain','TNT_ES','ES')
	),
	'Sweden' => array(
		array('DB Schenker Sweden','DBSCHENKER_SE','SE'),
		array('DirectLink Sweden','DIRECTLINK_SE','SE'),
		array('PostNord Logistics','POSTNORD_LOGISTICS_GLOBAL','SE'),
		array('PostNord Logistics Denmark','POSTNORD_LOGISTICS_DK','SE'),
		array('PostNord Logistics Sweden','POSTNORD_LOGISTICS_SE','SE')
	),
	'Switzerland' => array(
		array('Swiss Post','SWISS_POST','CH')
	),
	'Taiwan' => array(
		array('Chunghwa Post','CHUNGHWA_POST','TW'),
        array('Taiwan Post','TAIWAN_POST_TW','TW')
	),
	'Thailand' => array(
		array('Acommerce','ACOMMMERCE','TH'),
		array('Alphafast','ALPHAFAST','TH'),
		array('CJ Thailand','CJ_TH','TH'),
		array('FastTrack Thailand','FASTRACK','TH'),
		array('Kerry Express Thailand','KERRY_EXPRESS_TH','TH'),
		array('NIM Express','NIM_EXPRESS','TH'),
		array('Ninjavan Thailand','NINJAVAN_THAI','TH'),
		array('SendIt','SENDIT','TH'),
		array('Thailand Post','THAILAND_POST','TH')
	),
	'Turkey' => array(
		array('PTT Posta','PTT_POST','TR')
	),
	'Ukraine' => array(
		array('Nova Poshta','NOVA_POSHTA','UA'),
        array('Nova Poshta International','NOVA_POSHTA_INT','UA')
	),
	'United Arab Emirates' => array(
		array('AXL Express & Logistics','AXL','AE'),
		array('Continental','CONTINENTAL','AE'),
		array('Skynet Worldwide Express UAE','SKYNET_UAE','AE')
	),
	'United Kingdom' => array(
		array('Airborne Express UK','AIRBORNE_EXPRESS_UK','UK'),
		array('Airsure','AIRSURE','UK'),
		array('APC Overnight','APC_OVERNIGHT','UK'),
		array('Asendia UK','ASENDIA_UK','UK'),
		array('CollectPlus','COLLECTPLUS','UK'),
		array('Deltec UK','DELTEC_UK','UK'),
		array('DHL UK','DHL_UK','UK'),
		array('DPD Delistrack','DPD_DELISTRACK','UK'),
		array('DPD UK','DPD_UK','UK'),
		array('Fastway UK','FASTWAY_UK','UK'),
		array('HermesWorld','HERMESWORLD_UK','UK'),
		array('Interlink Express','INTERLINK','UK'),
		array('MyHermes UK','MYHERMES','UK'),
		array('Nightline UK','NIGHTLINE_UK','UK'),
		array('Parcel Force','PARCELFORCE','UK'),
		array('Royal Mail','ROYAL_MAIL','UK'),
		array('RPD2man Deliveries','RPD_2_MAN','UK'),
		array('Skynet Worldwide Express UK','SKYNET_UK','UK'),
		array('TNT UK','TNT_UK','UK'),
		array('UK Mail','UK_MAIL','UK'),
		array('Yodel','YODEL','UK')
	),
	'United States' => array(
		array('ABC Package Express','ABC_PACKAGE','US'),
		array('Airborne Express','AIRBORNE_EXPRESS','US'),
		array('Asendia USA','ASENDIA_US','US'),
		array('Cpacket','CPACKET','US'),
		array('Ensenda USA','ENSENDA','US'),
		array('Estes','ESTES','US'),
		array('Fastway USA','FASTWAY_US','US'),
		array('Globegistics USA','GLOBEGISTICS','US'),
		array('International Bridge','INTERNATIONAL_BRIDGE','US'),
		array('OnTrac','ONTRAC','US'),
		array('RL Carriers','RL_US','US'),
		array('RR Donnelley','RRDONNELLEY','US'),
		array('USPS','USPS','US')
	),
	'Vietnam' => array(
		array('Kerry Express Vietnam','KERRY_EXPRESS_VN','VN'),
		array('Vietnam Post','VIETNAM_POST','VN'),
		array('Vietnam Post EMS','VNPOST_EMS','VN')
	)
);

return $carriers;