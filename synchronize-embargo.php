<?php

// Database connect parameters

// Host name
$host = "localhost";
// Port number
$port = "5432";
// Database name
$db = "";
// User name
$user = "";
// Password
$pass = "";

// Metadata field IDs

// Embargo end metadata field ID
$embargoEndDateId = ;
// Access rights ID
$accessRightsId = ;

// Timezone
$timezone = "UTC";

$link = pg_connect("host=localhost port=".$port." dbname=".$db." user=".$user." password=".$pass)  or die("Couldn't Connect ".pg_last_error()); 

// Set timezone
date_default_timezone_set($timezone);

$i = 0;
$j = 0;

// Get all items
$allItems = pg_query("SELECT item_id FROM item");

while($item = pg_fetch_array($allItems, NULL, PGSQL_ASSOC)){

    // Get the latest embargo date from the item's files
    $fileEmbargoItems = pg_query("SELECT start_date
FROM resourcepolicy rp, bundle2bitstream b2b, item2bundle i2b
WHERE start_date > '".date("Y-m-d")."'
AND rp.resource_type_id = 0
AND b2b.bitstream_id = rp.resource_id
AND i2b.bundle_id = b2b.bundle_id
AND i2b.item_id = ".$item["item_id"]);

    $latestFileEmbargo = "";
    while($fileEmbargoItem = pg_fetch_array($fileEmbargoItems, NULL, PGSQL_ASSOC)){
		if($fileEmbargoItem["start_date"] > $latestFileEmbargo){
			$latestFileEmbargo = $fileEmbargoItem["start_date"];
		}
    }

    // Get embargo end date from metadata
    $embargoEndDates = pg_query("SELECT text_value FROM metadatavalue WHERE resource_id=".$item["item_id"]." AND resource_type_id=2 AND metadata_field_id=".$embargoEndDateId);
    
    $embargoEndDateMetadata = "";
    while($embargoEndDate = pg_fetch_array($embargoEndDates, NULL, PGSQL_ASSOC)){
		$embargoEndDateMetadata = $embargoEndDate["text_value"];
    }

    // Compare the two and set metadata to file value
    if($latestFileEmbargo != $embargoEndDateMetadata){
		echo "File embargo: ".$latestFileEmbargo." Metadata: ".$embargoEndDateMetadata." Item: ".$item["item_id"]."\n";
		$j++;

		// if $latestFileEmbargo == "" --> delete embargo end metadata and set rights = openAccess
		if($latestFileEmbargo == ""){
			pg_query("DELETE FROM metadatavalue WHERE resource_id=".$item["item_id"]." AND metadata_field_id=".$embargoEndDateId);
			pg_query("UPDATE metadatavalue SET text_value='openAccess' WHERE resource_id=".$item["item_id"]." AND metadata_field_id=".$accessRightsId);
		}
		// if $embargoEndDateMetadata == "" --> set emabargo end metadata = file embargo and set rights = embargoedAccess
		else if($embargoEndDateMetadata == ""){
			pg_query("INSERT INTO metadatavalue (resource_id, metadata_field_id, text_value, place, resource_type_id) VALUES (".$item["item_id"].",".$embargoEndDateId.",'".$latestFileEmbargo."',1,2)");
			pg_query("UPDATE metadatavalue SET text_value='embargoedAccess' WHERE resource_id=".$item["item_id"]." AND metadata_field_id=".$accessRightsId);
		}
		// if both are set --> Wrong date in embargo end metadata, set to file embargo and set rights = embargoedAccess
		else {
			pg_query("UPDATE metadatavalue SET text_value='".$latestFileEmbargo."' WHERE resource_id=".$item["item_id"]." AND metadata_field_id=".$embargoEndDateId);
			pg_query("UPDATE metadatavalue SET text_value='embargoedAccess' WHERE resource_id=".$item["item_id"]." AND metadata_field_id=".$accessRightsId);
		}
    }
    
    $i++;
}

echo $j." item(s) processed\n";
echo $i." item(s) total\n";

?>
