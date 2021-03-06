###
# This file designed to be included by Makefile.
###

.PHONY: helptest
helptest :
	@echo "Test Options:"
	@echo "  make test      # run unit tests as needed."
	@echo "  make cleantest # purge test data, forces full retest."

#### Testing stuff ####

.PHONY: test
test: test/motleyphp.tapchk
	auxchecktap $<
	@echo "[tests complete]"

gen/phpunit/html :
	mkdir -p $@

test/motleyphp.testdox: \
  phpunit.xml \
  gen/phpunit/html \
  $(wildcard test/*Test.php) \
  $(wildcard bin/*) \
  $(wildcard phplib/Motley/*.php)
	phpunit --verbose --configuration=phpunit.xml

test/motleyphp.taplog : test/motleyphp.testdox
	bin/motleyTestdoxToTap $< $@

test/motleyphp.tapchk : test/motleyphp.taplog
	auxchecktap $< > $@

#### Cleaning test logs ####

.PHONY: cleantest
cleantest:
	rm -f  test/motleyphp.testdox
	rm -f  test/motleyphp.taplog
	rm -f  test/motleyphp.tapchk
	rm -fr gen/phpunit
