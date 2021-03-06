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
	@echo "Other:"
	@echo "  make showdocwarn   # show documentation warnings."
	@echo "  make showcov       # show coverage."
	@echo "Web Publishing:"
	@echo "  make pubdocs       # publish doxygen and phpunit stuff to docs."
	@echo "  make cleanpub      # clean docs stuff."

.PHONY: build
build: bin \
  bin/motleyGuid \
  bin/motleyTestdoxToTap \
  gen/doxygen/html \
  gen/doxygen/html/index.html \
  gen/doxygen/latex/refman.pdf
	@echo "[build complete]"

bin:
	mkdir $@
	chmod $(DIRMODE) $@

bin/motleyGuid : phpcmd/motleyGuid.php
	cp $< $@
	chmod $(BINMODE) $@

bin/motleyTestdoxToTap : phpcmd/motleyTestdoxToTap.php
	cp $< $@
	chmod $(BINMODE) $@

gen/doxygen/html :
	mkdir -p $@

gen/doxygen/html/index.html : \
  Doxyfile \
  $(wildcard phplib/Motley/*.php)
	@echo "doxygen building documentation..."
	doxygen $< > gen/doxygen/doxygen.log 2>&1
	-grep -e '\(warning:\)\|\(error:\)' gen/doxygen/doxygen.log > gen/doxygen/doxygen.err
	cat gen/doxygen/doxygen.err

gen/doxygen/latex/refman.pdf : \
  gen/doxygen/html/index.html \
  gen/doxygen/latex/refman.tex
	@echo "generating '$@'..."
	cd gen/doxygen/latex; make > ../latex.log 2>&1

#### Other stuff ####
.PHONY: showdocwarn
showdocwarn:
	@cat gen/doxygen/doxygen.err

.PHONY: showcov
showcov:
	@tail --lines=+3 test/motleyphp.cov.txt \
          | sed -e s/^\ *$$//g \
          | sed -e s/\ $$//g \
          | sed -e /^\ *$$/d \
          | sed -e s/^\ S/S/g \
          | tail --lines=+7
	@tail --lines=+3 test/motleyphp.cov.txt \
          | sed -e s/^\ *$$//g \
          | sed -e s/\ $$//g \
          | sed -e /^\ *$$/d \
          | sed -e s/^\ S/S/g \
          | tail --lines=+3 \
          | head --lines=4

#### publist to website stuff
.PHONY: pubdocs
pubdocs:
	mkdir -p docs/doxygen
	mkdir -p docs/phpunit
	cp -a gen/doxygen/html docs/doxygen
	cp -a gen/phpunit/html docs/phpunit

.PHONY: cleanpub
cleanpub:
	rm -fr docs/doxygen
	rm -fr docs/phpunit

#### Cleaning built stuff ####

.PHONY: clean
clean: cleantest
	rm  -f bin/*
	rm -rf gen/doxygen
