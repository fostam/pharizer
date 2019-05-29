
TARGET_DIR=dist
TARGETS=pharizer

all: $(TARGETS)

pharizer: dep
	php src/pharizer.php build

dep:
	composer install --no-progress --no-dev --optimize-autoloader

dep-dev:
	composer install --no-progress

test:
	vendor/phpunit/phpunit/phpunit tests

clean:
	rm -rf $(TARGET_DIR)

distclean: clean
	rm -rf vendor

update:
	composer update
