# CHANGELOG

## 2.x

- (CHG) Use class constants for service and attributes names.

- (CHG) Upgrade to PHP 7.2+ only.

- (CHG) Drop HHVM support.

- (CHG) Upgrade Aura.Di dependency version to 4.0.o

- (CHG) 2.x is now the default PR branch.


## 1.1.0

Radar now retains the matched Route object as a Request attribute
under the key `radar/adr:route`.  If there was no matched route, or
routing failed, the attribute value will be `false`.
