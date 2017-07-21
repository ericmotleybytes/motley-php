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
  phplib/Motley/GuidGenerator.chk \
  doxygen/html \
  doxygen/html/index.html \
  doxygen/latex/refman.pdf
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

doxygen/html :
	mkdir -p $@

doxygen/html/index.html : \
  Doxyfile \
  $(wildcard phplib/Motley/*.php)
	@echo "doxygen building documentation..."
	doxygen $< > doxygen/doxygen.log 2>&1

doxygen/latex/refman.pdf : \
  doxygen/html/index.html \
  doxygen/latex/refman.tex
	@echo "generating '$@'..."
	cd doxygen/latex; make > ../latex.log 2>&1

#### Cleaning built stuff ####

.PHONY: clean
clean: cleantest
	rm  -f phpcmd/*.chk
	rm  -f phplib/Motley/*.chk
	rm  -f bin/*
	rm -rf doxygen
