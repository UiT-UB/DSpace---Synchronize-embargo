# DSpace-Synchronize-embargo
Synchronize file embargo and metadata embargo values in DSpace

This script reads file/bitstream embargo values from items in DSpace and sets a metadata field to the same value. This might be useful when you are already using the file embargo feature, but also have implemented the requirements for compliance with the OpenAIRE guidelines. 

These guidelines require certain metadata fields for items under embargo: A dc:rights field which indicates access level, and, if the access level is set to embargoed access, a dc:date field indicating the embargo end date.

To ease the administration of such items, this script checks if an item has any files under embargo. If so, the dc:rights metadata field is set to embargoedAccess and the dc:date field is set to the latest embargo end date found among the item's files. If an item has no files left under embargo, the script sets the dc:rights field to openAccess and removes the dc:date embargo field. In the rare case where there is a difference between the file embargo date and the dc:date embargo metadata, the latter is set to the file embargo value.

The script should be run as a cronjob to ensure correct metadata values at any time.
