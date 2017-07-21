###
# This file designed to be included by Makefile.
###

.PHONY: helpbuild
helpbuild:
	@echo "Common building usage:"
	@echo "  make helpbuild     # display this screen."
	@echo "  make build         # build stuff."
	@echo "Common build cleanup usage:"
	@echo "  make clean         # clean stuff."

.PHONY: build
build: bin \
  bin/motleyGuid \
  bin/motleyTestdoxToTap \
  phplib/Motley/GuidGenerator.chk
	@echo "[build complete]"

bin:
	mkdir $@
	chmod $(DIRMODE) $@

bin/motleyGuid : phpcmd/motleyGuid.php phpcmd/motleyGuid.chk
	cp $< $@
	chmod $(BINMODE) $@

phpcmd/motleyGuid.chk : phpcmd/motleyGuid.php
	php --syntax-check $< > $@

bin/motleyTestdoxToTap : phpcmd/motleyTestdoxToTap.php phpcmd/motleyTestdoxToTap.chk
	php --syntax-check $<
	cp $< $@
	chmod $(BINMODE) $@

phpcmd/motleyTestdoxToTap.chk : phpcmd/motleyTestdoxToTap.php
	php --syntax-check $< > $@

phplib/Motley/GuidGenerator.chk : phplib/Motley/GuidGenerator.php
	php --syntax-check $< > $@

#### Cleaning built stuff ####

.PHONY: clean
clean: cleantest
	rm -f phpcmd/*.chk
	rm -f phplib/Motley/*.chk
	rm -f bin/*
