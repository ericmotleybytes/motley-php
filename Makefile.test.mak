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

test/motleyphp.testdox: \
  $(wildcard test/*Test.php) \
  $(wildcard bin/*) \
  $(wildcard phplib/Motley/*.php)
	phpunit test --testdox-text $@ --whitelist phplib --coverage-text=test/motleyphp.cov.txt

test/motleyphp.taplog : test/motleyphp.testdox
	bin/motleyTestdoxToTap $< $@

test/motleyphp.tapchk : test/motleyphp.taplog
	auxchecktap $< > $@

#### Cleaning test logs ####

.PHONY: cleantest
cleantest:
	rm -f test/motleyphp.testdox
	rm -f test/motleyphp.taplog
	rm -f test/motleyphp.tapchk
