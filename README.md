# CPBot
需要的PHP版本：![PHP-Version](https://img.shields.io/badge/php-5.3.3%2B-blue)

CPBot(CQHTTP PHP Bot)是基于 [Workerman](https://www.workerman.net/) 与 [go-cqhttp](https://github.com/Mrs4s/go-cqhttp) 制作的PHP SDK，旨在帮助PHP用户快速开发和使用go-cqhttp机器人。

当前已支持大部分go-cqhttp列出并支持的所有API、事件
## 开始使用
请下载并安装[go-cqhttp](https://github.com/Mrs4s/go-cqhttp)

编辑插件配置文件，在需要的账号下打开WS连接

在[Releases](https://github.com/endymx/MPBot/releases)下载MPBot最新的版本

配置环境，PHP版本应>=5.4，建议>=7.0，使用>=8.0会获得更高的性能

### Windows下
下载PHP：[PHP Windows 下载方法](https://www.workerman.net/windows)

下载解压后配置Windows系统变量

在CPBot目录下新建文件start.bat，右键-编辑，写入`php start.php start`并保存

双击start.bat使用

### Linux下
配置PHP环境，并安装pcntl、posix扩展

建议安装event或者libevent扩展，但不是必须的（注意event扩展需要PHP>=5.4）

Linux用户可以运行以下脚本检查本地环境是否满足PHP要求

`curl -Ss http://www.workerman.net/check.php | php`

如果脚本中全部提示ok，则代表满足PHP运行环境

运行`php start.php start`使用，如需守护进程启动可运行`php start.php start -d`或使用screen

## 开发
模板请参考并使用`start.php`文件

Docs：暂时未写

当前版本已经可以正常使用，但是还有些API需要更新。

## 更多信息

* [go-cqhttp](https://github.com/Mrs4s/go-cqhttp)
* [Workerman](https://www.workerman.net/)