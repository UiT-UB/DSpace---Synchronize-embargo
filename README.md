# DSpace-Synchronize-embargo
Synchronize file embargo and metadata embargo values in DSpace

This script reads file/bitstream embargo values from items in DSpace and sets a metadata field to the same value. This might be useful when you are already using the file embargo feature, but also have implemented the requirements for compliance with the OpenAIRE guidelines (https://guidelines.openaire.eu/en/latest/literature/index.html). 

These guidelines require certain metadata fields for items under embargo: A dc:rights field which indicates access level, and, if the access level is set to embargoed access, a dc:date field indicating the embargo end date.

To ease the administration of such items, this script checks if an item has any files under embargo. If so, the dc:rights metadata field is set to embargoedAccess and the dc:date field is set to the latest embargo end date found among the item's files. If an item has no files left under embargo, the script sets the dc:rights field to openAccess and removes the dc:date embargo field. In the rare case where there is a difference between the file embargo date and the dc:date embargo metadata, the latter is set to the file embargo value.

The script should be run as a cronjob to ensure correct metadata values at any time.

## Requirements
This script has been written for a DSpace 5 installation with Postgres 9.6 and PHP 5. It is not guaranteed to work for other versions of the prerequisite software.

## Configuration
The script requires some configuration parameters that need to be set in the script itself. These are for database connection, the metadata field IDs for the two metadata fields involved and timezone settings. Some of the fields have default values (in parathesis):

#### Database connection parameters:
- Host name ("localhost")
- Port number ("5432")
- Database name
- User name
- Password
  
#### Metadata field IDs:
- dc:date embargo field
- dc:rights acces rights field
  
#### Other
- Timezone ("UTC")
