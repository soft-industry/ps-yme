
dist:
	composer install
	( cd .. && zip -q -r yme yme -x yme/.git/\* yme/Makefile ) && mv ../yme.zip .
    
clean:
	rm -f yme.zip

