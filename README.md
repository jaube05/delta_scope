# delta_scope
ACT - Intranet Site
User can upload csv file of 'Scope' taxonomy terms for creating, updating or deleting terms.

This custom module will create a back end page for a user to upload a csv file. 
A batch script will run to read through the csv file. All terms will be compared to the current database. 
Will either create, update or delete terms and its children. Then trigger the update_taxonomy_term custom module to 
update all nodes using and terms that were updated or deleted.
