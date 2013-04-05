<?php /*

[properties/random-image]

label = "Random Image"
icon = picture

path[label] = Folder
path[type] = select
path[require] = "apps/filemanager/lib/Functions.php"
path[callback] = "filemanager_list_folders"

desc[label] = Show descriptions
desc[type] = select
desc[require] = "apps/filemanager/lib/Functions.php"
desc[callback] = "filemanager_yes_no"

[properties/index]

label = "Properties"

type[label] = Type
type[type] = select
type[require] = "apps/properties/lib/Property.php"
type[callback] = "Property::property_types"

; */ ?>
