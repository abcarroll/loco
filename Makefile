COMPOSER    = composer
VENDORBIN   = vendor/bin


clean: autoload
	@rm -rf -- *.cache
	@rm -rf -- composer.lock

fix: phpcbf php-cs-fixer-fix psalter
check: phpcs php-cs-fixer psalm phpstan
analyze: phploc pdepends phpstan-deps

php-lint:
	./vendor/bin/parallel-lint src/ tests/ docs/examples/

autoload:
	@$(COMPOSER) --dev dump-autoload

phpcs: mkmetrics
	@$(VENDORBIN)/phpcs --colors -wp  --standard=PSR2,.phpcs.xml src/ tests/ docs/examples | tee .metrics/phpcs.txt

phpcbf: mkmetrics
	@$(VENDORBIN)/phpcbf --colors -wp  --standard=PSR2,.phpcs.xml src/ tests/ docs/examples | tee .metrics/phpcf.txt

php-cs-fixer: mkmetrics
	./vendor/bin/php-cs-fixer fix --diff --dry-run --config ./.php_cs.php src/ tests/ docs/examples/ | tee .metrics/php-cs-fixer.txt

php-cs-fixer-fix: mkmetrics
	./vendor/bin/php-cs-fixer fix --diff --dry-run --config ./.php_cs.php src/ tests/ docs/examples/ | tee .metrics/php-cs-fixer-fix.txt

phpstan: mkmetrics
	./vendor/bin/phpstan analyse --level 7 --ansi src/ | tee .metrics/phpstan.txt

psalm: mkmetrics
	./vendor/bin/psalm --config=.psalm.xml src/ tests/ docs/examples | tee .metrics/psalm.txt

psalter: mkmetrics
	./vendor/bin/psalter --dry-run --config=.psalm.xml --issues=InvalidFalsableReturnType,InvalidNullableReturnType,InvalidReturnType,LessSpecificReturnType,MismatchingDocblockParamType,MismatchingDocblockReturnType,MissingClosureReturnType,MissingParamType,MissingReturnType,PossiblyUndefinedGlobalVariable,PossiblyUndefinedVariable,PossiblyUnusedProperty,UnusedProperty,UnusedVariable,UnnecessaryVarAnnotation src/ tests/ docs/examples | tee .metrics/psalter.txt

phpmd: mkmetrics
	./vendor/bin/phpmd src/ text cleancode,codesize,controversial,design,naming,unusedcode | tee .metrics/phpmd.txt
	./vendor/bin/phpmd tests/ text cleancode,codesize,controversial,design,naming,unusedcode | tee .metrics/phpmd.txt
	./vendor/bin/phpmd docs/examples/ text cleancode,codesize,controversial,design,naming,unusedcode | tee .metrics/phpmd.txt


phploc: mkmetrics
	./vendor/bin/phploc src/ tests/ docs/examples/ | tee .metrics/phploc.txt

pdepends: mkmetrics
	./vendor/bin/pdepend --summary-xml=.metrics/dependency.xml --jdepend-chart=.metrics/pdepend.svg src/,tests/,docs/examples/ | tee .metrics/pdepends.txt

phpstan-deps: mkmetrics
	./vendor/bin/phpstan dump-deps --no-ansi --configuration=.phpstan.neon src/ tests/ docs/examples/

mkmetrics:
	@mkdir -p .metrics/
