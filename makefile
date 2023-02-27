PHP = php
SOURCE = parse.php

run:
	$(PHP) -d open_basedir="." -f $(SOURCE) -- --help<testiky/sada2.txt
	