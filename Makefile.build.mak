###
# This file designed to be included by Makefile.
###

.PHONY: helpbuild
helpbuild:
	@echo "Common building usage:"
	@echo "  make helpbuild     # display this screen."
	@echo "  make build         # build stuff."
	@echo "  make test          # run tests."
	@echo "  make checktest     # run tests and remember results."
	@echo "Common build cleanup usage:"
	@echo "  make clean         # clean stuff."

.PHONY: build
build: bins

.PHONY: bins
bins: bin bin/motley_guid

bin:
	mkdir $@
	chmod $(DIRMODE) $@

bin/motley_guid : phpcmd/motley_guid.php
	php --syntax-check $<
	cp $< $@
	chmod $(BINMODE) $@

#### Cleaning built stuff ####

.PHONY: clean
clean:
	rm -f bin/motley_guid
