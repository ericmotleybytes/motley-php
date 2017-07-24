###
# This file designed to be included by Makefile.
###

#### install help stuff ####
.PHONY: helpinstall
helpinstall:
	@echo "Common installing usage:"
	@echo "  make install       # copy files to under $(DEV_PREFIX2)."
	@echo "  make sysinstall    # copy files to under $(SYS_PREFIX) as root."
	@echo "  make install PREFIX=<prefix> # custom copy files."
	@echo "Common uninstalling usage:"
	@echo "  make uninstall     # delete files installed under $(DEV_PREFIX2)."
	@echo "  make sysuninstall  # delete files installed under $(SYS_PREFIX) as root."
	@echo "  make uninstall PREFIX=<prefix> # custom uninstall files."

#### install stuff ####

.PHONY: install
install : installinfo \
  $(PREFIX)/bin \
  $(PREFIX)/bin/motleyGuid \
  $(PREFIX)/bin/motleyTestdoxToTap \
  $(PREFIX)/phplib \
  $(PREFIX)/phplib/Motley \
  $(PREFIX)/phplib/Motley/GuidGenerator.php \
  $(PREFIX)/phplib/Motley/CommandLineArgumentSyntax.php \
  installinfo2

.PHONY: installinfo
installinfo:
	@echo "[INFO: doing install under $(PREFIX) as user $$(whoami).]"

.PHONY: installinfo2
installinfo2:
	@echo "[INFO: installed under $(PREFIX) as user $$(whoami).]"
	@fullprefix=$$(readlink -f $(PREFIX))

$(PREFIX)/bin:
	mkdir "$@"
	chmod $(DIRMODE) "$@"

$(PREFIX)/bin/motleyGuid : bin/motleyGuid
	cp -a "$<" "$@"
	chmod $(BINMODE) "$@"

$(PREFIX)/bin/motleyTestdoxToTap : bin/motleyTestdoxToTap
	cp -a "$<" "$@"
	chmod $(BINMODE) "$@"

$(PREFIX)/phplib:
	mkdir "$@"
	chmod $(DIRMODE) "$@"

$(PREFIX)/phplib/Motley:
	mkdir "$@"
	chmod $(DIRMODE) "$@"

$(PREFIX)/phplib/Motley/GuidGenerator.php : phplib/Motley/GuidGenerator.php
	cp -a "$<" "$@"
	chmod $(SRCMODE) "$@"

$(PREFIX)/phplib/Motley/CommandLineArgumentSyntax.php : \
  phplib/Motley/CommandLineArgumentSyntax.php
	cp -a "$<" "$@"
	chmod $(SRCMODE) "$@"

#### uninstall stuff ####

.PHONY: uninstall
uninstall:
	@echo "INFO: doing uninstall from $(PREFIX) as user $$(whoami)."
	rm -f $(PREFIX)/bin/motleyGuid
	rm -f $(PREFIX)/bin/motleyTestdoxToTap
	rm -f $(PREFIX)/phplib/Motley/GuidGenerator.php
	rm -f $(PREFIX)/phplib/Motley/CommandLineArgumentSyntax.php

#### sys stuff ####

.PHONY: sysinstall
sysinstall:
	@sudo $(MAKE) "$(THISFILE)" install PREFIX="$(SYS_PREFIX)"

.PHONY: sysuninstall
sysuninstall:
	@sudo $(MAKE) $(THISFILE) uninstall PREFIX=$(SYS_PREFIX)
