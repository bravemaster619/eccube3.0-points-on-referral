# PointsOnReferral
PointsOnReferral is a plugin for [EC-CUBE v3.0](https://doc.ec-cube.net/) that rewards a user points on referrals.

# Requirement
- [EC-CUBE ^3.0.10](https://github.com/EC-CUBE/ec-cube/releases)
- [Point Plugin](https://www.ec-cube.net/products/detail.php?product_id=1101)

# EC-CUBE Plugin Config
- name: 友達紹介ポイント
- code: PointsOnReferral

# Installation Guide
## By EC-CUBE Admin Dashboard
    1. Download zip file
    2. Upload the zip file in plugin management page
## By Console

1. Install

In root directory of the eccube project, run the following command in shell:
```console
$ php app/console plugin:develop install --code PointsOnReferral

```

2. Enable

In root directory of the eccube project, run the following command in shell:
```console
$ php app/console plugin:develop enable --code PointsOnReferral
```
3. Disable

```console
$ php app/console plugin:develop disable --code PointsOnReferral
```

4. Uninstall

```console
$ php app/console plugin:develop uninstall --code PointsOnReferral
```

*This will not remove the plugin source code, in contrast to admin dashboard where uninstalling the plugin will remove the source code.*

# Test

In root directory of the eccube project, run the following command in shell:
```console
$ vendor/bin/phpunit app\Plugin\PointsOnReferral
```
