<html>
    <head>
        <title>Price Comparison Engine</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="bootstrap-theme.min.css">
        <script src="jquery-3.2.1.js"></script>
        <script src="bootstrap.min.js"></script>
    </head>

    <body style="background-color: #F0F0F0">

    	<nav class="navbar navbar-inverse" style="margin-bottom:0;">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php">Bazaar Raja</a>
    </div>
    <ul class="nav navbar-nav navbar-right">
      <li class="active"><a href="index.php">Home</a></li>
      <li><a href="about.php">About Us</a></li>
      <li><a href="contact.php">Contact Us</a></li>
    </ul>>
   </div>
</nav>

        <div class="jumbotron text-center pagination-centered" >
            <h2>Price Comparison Engine</h2>
            <form class="form">
                <input type="text" size="30" name="url"/>
                
                <input class="btn btn-primary" type="submit" value="Search" name="sbtn"/>

            </form>
        </div>


		<div class="row">
		<div class="col-sm-1"></div>
                <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);

if (isset($_GET['sbtn']) && isset($_GET['url']))
	{
	$queryString = $_GET['url'];
	$queryString = str_replace(' ', '+', $queryString);
	$count = 5;
	if ($queryString == null)
		{
		echo "<script type='text/javascript'>alert('Please enter the product name!')</script>";
		exit();
		}

	flipkart($queryString, $count);
	amazon($queryString);
	}

function flipkart($queryString, $count)
	{
	$flipkartUrl = "https://affiliate-api.flipkart.net/affiliate/search/json?query=" . $queryString . "&resultCount=" . $count;
	$aHTTP['http']['method'] = 'GET';
	$aHTTP['http']['header'] = "Fk-Affiliate-Token:f7a81c2e40b6447b82182c13d7a10c42\r\n";
	$aHTTP['http']['header'].= "Fk-Affiliate-Id:infobazaa1\r\n";
	$request = stream_context_create($aHTTP);
	$response = file_get_contents($flipkartUrl, false, $request);
	$array = json_decode($response, true);
	$x = 0; ?>
		<div class="col-sm-5">
        <div class="panel panel-info">
            <div class="panel-heading">Flipkart</div>
<?php
	while ($x < count($array['productInfoList']))
		{
		$title = $array['productInfoList'][$x]['productBaseInfo']['productAttributes']['title'];
		$amountFake = $array['productInfoList'][$x]['productBaseInfo']['productAttributes']['maximumRetailPrice']['amount'];
		$amountReal = $array['productInfoList'][$x]['productBaseInfo']['productAttributes']['sellingPrice']['amount'];
		$productUrl = $array['productInfoList'][$x]['productBaseInfo']['productAttributes']['productUrl'];
		$imageUrl   = $array['productInfoList'][$x]['productBaseInfo']['productAttributes']['imageUrls']['200x200'];
?>
			<div class="panel panel-default">
						<div class="panel-body">
						<img src="<?php echo $imageUrl; ?>" class="col-sm-3">
						<div class="col-sm-8"><h4><?php echo $title; ?></h4><hr/>
						<p class="col-sm-8">Rs <?php echo $amountReal; ?></p>
						<a href ="<?php echo $productUrl; ?>" target="_blank"> <button class="btn btn-primary col-sm-4">Buy</button></a>
                        </div></div></div>
                        <?php
		$x++;
		} // end of while
	 ?>
        </div></div>
                <?php
	}

?>


<?php

function amazon($queryString)
	{

	// Your AWS Access Key ID, as taken from the AWS Your Account page

	$aws_access_key_id = "";

	// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page

	$aws_secret_key = "Jm1aSpOnyI7yt4j2e9Il4oKhI1zhPUp/mXgXuwoe";

	// The region you are interested in

	$endpoint = "webservices.amazon.in";
	$uri = "/onca/xml";
	$params = array(
		"Service" => "AWSECommerceService",
		"Operation" => "ItemSearch",
		"AWSAccessKeyId" => "",
		"AssociateTag" => "bazaaraja-21",
		"SearchIndex" => "All",
		"ResponseGroup" => "Images,ItemAttributes",
		"Keywords" => "$queryString"
	);

	// Set current timestamp if not set

	if (!isset($params["Timestamp"]))
		{
		$params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
		}

	// Sort the parameters by key

	ksort($params);
	$pairs = array();
	foreach($params as $key => $value)
		{
		array_push($pairs, rawurlencode($key) . "=" . rawurlencode($value));
		}

	// Generate the canonical query

	$canonical_query_string = join("&", $pairs);

	// Generate the string to be signed

	$string_to_sign = "GET\n" . $endpoint . "\n" . $uri . "\n" . $canonical_query_string;

	// Generate the signature required by the Product Advertising API

	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));

	// Generate the signed URL

	$request_url = 'http://' . $endpoint . $uri . '?' . $canonical_query_string . '&Signature=' . rawurlencode($signature);

	// echo "Signed URL: \"".$request_url."\"";

	$xmldoc = file_get_contents("$request_url");
	$xml = new SimpleXMLElement($xmldoc);
	$count = $xml->Items->Item->count();
	$asin = array();
	$title = array();
	$url = array();
	$price = array(); ?>
		
		<div class="col-sm-5">
        <div class="panel panel-info">
            <div class="panel-heading">Amazon</div>
<?php
	for ($i = 0; $i < $count - 5; $i++)
		{
		$asin[$i] = $xml->Items->Item[$i]->ASIN;
		$title[$i] = $xml->Items->Item[$i]->ItemAttributes->Title;
		$imgurl[$i] = $xml->Items->Item[$i]->MediumImage->URL;
		$url[$i] = $xml->Items->Item[$i]->DetailPageURL;
		
		$price[$i] = getPrice($asin[$i]);
		
?>

<div class="panel panel-default">
						<div class="panel-body">
						<img src="<?php echo $imgurl[$i]; ?>" class="col-sm-3">
						<div class="col-sm-8"><h4><?php echo $title[$i]; ?></h4><hr/>
						<p class="col-sm-8">Rs <?php echo $price[$i]; ?></p>
						<a href ="<?php echo $url[$i]; ?>" target="_blank"> <button class="btn btn-primary col-sm-4">Buy</button></a>
                        </div></div></div>
	<?php
		} ?>
</div></div>
<?php
	}

// ***************************

function getPrice($param)
	{

	// Your AWS Access Key ID, as taken from the AWS Your Account page

	$aws_access_key_id = "";

	// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page

	$aws_secret_key = "";

	// The region you are interested in

	$endpoint = "webservices.amazon.in";
	$uri = "/onca/xml";
	$params = array(
		"Service" => "AWSECommerceService",
		"Operation" => "ItemLookup",
		"ResponseGroup" => "Offers",
		"IdType" => "ASIN",
		"ItemId" => "$param",
		"AWSAccessKeyId" => "",
		"AssociateTag" => "bazaaraja-21"
	);

	// Set current timestamp if not set

	if (!isset($params["Timestamp"]))
		{
		$params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
		}

	// Sort the parameters by key

	ksort($params);
	$pairs = array();
	foreach($params as $key => $value)
		{
		array_push($pairs, rawurlencode($key) . "=" . rawurlencode($value));
		}

	// Generate the canonical query

	$canonical_query_string = join("&", $pairs);

	// Generate the string to be signed

	$string_to_sign = "GET\n" . $endpoint . "\n" . $uri . "\n" . $canonical_query_string;

	// Generate the signature required by the Product Advertising API

	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));

	// Generate the signed URL

	$request_url = 'http://' . $endpoint . $uri . '?' . $canonical_query_string . '&Signature=' . rawurlencode($signature);

	// echo "Signed URL: \"".$request_url."\"";
	try{
	$xmldoc = file_get_contents("$request_url");
	$xml = new SimpleXMLElement($xmldoc);
	}catch (Exception $e){
	return '';
	}
	return $xml->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice;
	}

?>
			
<div class="col-sm-2"></div>
</div>

<nav class="navbar navbar-default navbar-fixed-bottom">
  <div class="container-fluid ">
	<h4 class="text-center"><p>&copy; 2017 BazaarRaja.in<p></h4>
  </div>
</nav> 
    </body>
</html>
