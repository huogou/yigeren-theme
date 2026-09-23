# yigeren-theme · 一个人的互联网笔记

> 一个温暖、留白、慢节奏的个人博客 WordPress 主题。像日记本，不像网站。
>
> 本文档为自包含项目说明，涵盖项目简介、目录结构、技术栈、本地运行、部署、配置与风险注意事项。
> 可直接整体投喂给任意 AI 以获得项目上下文，无需额外补充材料。

---

## 一、AI 协作速览（给接手 AI 的最短上下文）

| 项 | 内容 |
| --- | --- |
| 项目性质 | WordPress **自研主题**（非插件、非整站），纯原生 PHP 模板 |
| 主题标识 | `yigeren`（Text Domain），版本 `1.0.0` |
| 线上环境 | WordPress `6.8.3` + PHP `≥7.4` + MariaDB，阿里云轻量应用服务器，宝塔面板 |
| 站点地址 | `https://yaoqiang.xin`（主题头记录的 Theme URI 为 `https://yigeren.blog`，两者不一致，**待核实**） |
| 构建方式 | **无构建**。改完 PHP/CSS 直接上传到服务器生效，无 npm、无编译、无产物目录 |
| 最大改动风险 | ①分类 slug 硬编码在 `functions.php`；②`footer.php`/`page-about.php` 内嵌 67.8 KB base64 图片；③模板内硬编码绝对域名 |
| 数据依赖 | 主题**只含代码，不含文章数据**。文章、分类、媒体库存在数据库与 `wp-content/uploads`，不在本仓库 |

**修改前必读**：本主题把内容结构（6 个分类 slug、2 个页面 slug、文章级自定义字段 + 项目级 5 字段）写死在代码里，
仅同步代码而不配置对应的 WordPress 内容结构，页面会出现空白或降级显示。

---

## 二、目录结构

```
yigeren-theme/                      # 仓库根目录 = WordPress 主题目录（需整体放入 themes/）
├── style.css                       # 【必需】主题头信息 + 全站样式（1027 行，含 Design Tokens）
├── functions.php                   # 主题功能：主题支持、菜单、缩略图尺寸、业务工具函数
├── header.php                      # 全站页头：导航、阅读进度条容器、颗粒噪点 SVG
├── footer.php                      # 全站页脚：备案号、滚动动画/进度条 JS、「孩子回家」二维码弹窗
├── front-page.php                  # 首页模板：Hero 大图 + 分类入口 + 最近时间线
├── index.php                       # 兜底列表模板（同时承载搜索结果）
├── single.php                      # 文章详情页
├── category.php                    # 分类页（按 slug 差异化布局，moto 有专属版式）
├── page-archive.php                # 页面模板「归档页」：按年/月两级分组
├── page-about.php                  # 关于页
├── .gitignore                      # 屏蔽备份文件、编辑器产物、凭据类文件
└── README.md                       # 本文档
```

> **本地存在但未入库的文件**（服务器上客观存在，已下载到磁盘，仅被 Git 忽略，未被删除）：
> `_backup_20260807/`、`style.css.bak`、`front-page.php.bak`、`front-page.php.bak2`、`page-about.php.bak`

---

## 三、技术栈

| 层 | 选型 | 说明 |
| --- | --- | --- |
| CMS | WordPress 6.8.3（`Requires at least: 6.0`） | 主题遵循经典主题（Classic Theme）模板层级，**非区块主题**，无 `theme.json` |
| 服务端 | PHP ≥ 7.4 | 原生模板语法，无 Composer 依赖 |
| 数据库 | MariaDB / MySQL | 由 WordPress 管理，主题不直接读写数据库 |
| 样式 | 纯 CSS + CSS 自定义变量 | 无 Sass / PostCSS / Tailwind；设计令牌集中在 `style.css` 顶部 `:root` |
| 脚本 | 原生 JavaScript | 内联于 `footer.php`：`IntersectionObserver` 滚动揭示、滚动阅读进度条、二维码弹窗 |
| 字体 | Google Fonts 镜像 `fonts.loli.net` | Noto Serif SC / Noto Sans SC / EB Garamond / Caveat |
| 特效 | SVG `feTurbulence` | 全屏颗粒噪点遮罩（`.grain`） |
| 同步工具 | Python 3 + paramiko（SFTP） | `sync_theme.py`，见第九节 |

---

## 四、设计令牌（`style.css` 顶部 `:root`）

修改视觉风格**优先改这里的变量**，不要散改具体选择器：

| 变量 | 默认值 | 用途 |
| --- | --- | --- |
| `--seed-bg` | `#1A1614` | 页面底色（暖调深棕，非纯黑） |
| `--seed-surface` | `#242019` | 卡片/表面层 |
| `--seed-fg` | `#F2EBE0` | 主文字色（米白） |
| `--seed-muted` | `#9A8E82` | 次要文字 |
| `--seed-border` | `#352E27` | 分隔线 |
| `--seed-accent` | `#D4572A` | 强调色（橙红） |
| `--seed-radius` | `2px` | 全局圆角（刻意极小，保持克制感） |
| `--seed-spacing` / `--type-scale` | `1` | 间距与字号缩放系数，可整体调版式松紧 |
| `--grain-opacity` | `0.035` | 噪点强度 |
| 字体族 | Noto Serif SC / Noto Sans SC / EB Garamond / Caveat | 标题衬线、正文无衬线、英文强调、手写体 |

---

## 五、内容结构依赖（部署正确显示的前提）

### 5.1 必须存在的分类（slug 必须完全一致）

`functions.php` 中以数组硬编码中文名 / 英文名 / 汉字标识 / 描述：

| slug | 中文 | 英文 | 汉字 | 描述 |
| --- | --- | --- | --- | --- |
| `life` | 生活 | Life | 日 | 最近在骑车、拍照和陪猫晒太阳。 |
| `moto` | 摩托 | Riding | 道 | 两个轮子，一条路，够了。 |
| `cat` | 猫咪 | Cat | 猫 | 年糕，一只橘猫，正在认真地长大。 |
| `photo` | 摄影 | Photography | 光 | 用相机记住那些不值得写文章但值得记住的瞬间。 |
| `notes` | 笔记 | Notes | 筆 | 想到什么就记下来，不一定完整，但值得留下。 |
| `projects` | 折腾 | Projects | 造 | 记录那些利用互联网、AI和兴趣做出来的小东西，有的是完整作品，有的是一次尝试。 |

未命中映射的分类会**直接回显 slug 原文**（`yigeren_category_label()` 的兜底逻辑）。
分类 URL 为**根级形态**（`/life/`、`/projects/`），由 `functions.php` 的 `yigeren_root_category_rewrites()` 在 `init` 时为每个分类注册重写规则；新增分类后需 `wp rewrite flush` 一次。

### 5.2 必须存在的页面

| 页面 slug | 使用模板 | 说明 |
| --- | --- | --- |
| `archive` | `page-archive.php`（后台模板名：**归档页**） | `header.php` 的兜底菜单按 `get_page_by_path('archive')` 查找 |
| `about` | `page-about.php` | 同上，按路径 `about` 查找 |

### 5.3 可选自定义字段（文章级）

| meta key | 用途 | 出现位置 |
| --- | --- | --- |
| `_yigeren_mood` | 文章「心情」标签，缺省时取首个分类的中文名 | `functions.php: yigeren_post_mood()` |
| `_yigeren_distance` | 骑行距离，形如 `128 km` | `single.php` 文章头部 meta |
| `_yigeren_location` | 地点 | `single.php` 文章头部 meta |

### 5.4 项目字段（仅 projects 分类文章使用）

| meta key | 取值 | 说明 |
| --- | --- | --- |
| `project_status` | `进行中` / `已完成` | 项目状态；缺省视为「进行中」 |
| `project_start_date` | 日期字符串 | 项目开始时间（策划补充真实时间，未确认则留空） |
| `project_update_date` | 日期字符串 | 更新时间，作为项目排序依据；留空时前台隐藏日期、排序兜底用 WP `post_modified` |
| `project_link` | URL | 项目在线地址 |
| `project_cover` | 图片 URL | 项目封面；留空时前台显示「造」字斜纹占位 |

> 项目排序规则（`yigeren_projects_sorted()`）：按 `project_update_date` 降序（缺省回退 `post_modified`），
> 同时间「进行中」优先。分类页按「进行中分组在前、已完成随后」展示。

### 5.5 其他配置点

- **菜单位置**：`primary`（主导航）、`social`（社交链接）。未分配菜单时走 `header.php` 中的 `yigeren_fallback_menu()` 自动生成。
- **缩略图尺寸**：`article-cover` 880×495、`card-cover` 520×347、`photo-large` 680×453。
  **更换主题或新增尺寸后需重新生成缩略图**（如 Regenerate Thumbnails 插件），否则旧图不生效。
- **备案号**：`蜀ICP备2026022879号-1`，硬编码在 `footer.php`，链接至 `beian.miit.gov.cn`。
- **评论**：`functions.php` 已全局关闭评论与 ping，并移除 feed 链接。**若需开启评论，需先删除该段代码。**

---

## 六、本地运行

主题**不能单独运行**，必须挂载在 WordPress 环境中。以下为三种可行方案。

### 方案 A：Docker（推荐，环境最干净）

在本仓库同级目录创建 `docker-compose.yml`：

```yaml
services:
  db:
    image: mariadb:10.11
    environment:
      MARIADB_ROOT_PASSWORD: root
      MARIADB_DATABASE: wordpress
    volumes: [db_data:/var/lib/mysql]
  wordpress:
    image: wordpress:6.8-php8.2
    depends_on: [db]
    ports: ["8080:80"]
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: root
      WORDPRESS_DB_PASSWORD: root
      WORDPRESS_DB_NAME: wordpress
    volumes:
      - ./yigeren-theme:/var/www/html/wp-content/themes/yigeren-theme
volumes:
  db_data:
```

启动：`docker compose up -d` → 访问 `http://localhost:8080` → 后台启用主题。
修改本地文件即时生效（目录已挂载），无需重建。

### 方案 B：本地 PHP + MySQL（Windows）

1. 安装 PHP ≥ 7.4、MariaDB/MySQL（推荐使用 phpStudy / 小皮面板 / XAMPP 一键环境）。
2. 下载 WordPress 6.8.3 并解压到站点根目录。
3. 将本目录整体复制/软链到 `wp-content/themes/yigeren-theme`。
4. 访问站点完成安装，后台「外观 → 主题」启用。

### 方案 C：直接在服务器改（不推荐但现状如此）

服务器为宝塔面板环境，可直接在文件管理器编辑 `/www/wwwroot/wordpress/wp-content/themes/yigeren-theme/`。
**风险**：无版本回溯，改坏即线上事故。建议改用本仓库的工作流。

---

## 七、部署步骤

1. **改前备份**：服务器上复制一份主题目录为 `yigeren-theme.bak`（这也是仓库中大量 `.bak` 文件的来源）。
2. **上传**：任选其一
   - SFTP 覆盖上传至 `/www/wwwroot/wordpress/wp-content/themes/yigeren-theme/`；
   - 宝塔面板打包 zip 上传后解压覆盖（注意解压后目录层级不要多出一层）；
   - 使用本仓库的同步脚本（见第九节，支持上传模式）。
3. **校验**：`ls -la` 确认文件属主为 `www`（权限异常会导致 WordPress 读取失败）。
4. **刷新**：
   - `style.css` 的版本号由 `filemtime()` 自动生成，文件变更后缓存自动失效；
   - 若服务器开启 CDN / 浏览器强缓存，执行一次强制刷新（Ctrl+F5）。
5. **回归验证**：首页 Hero、分类页（重点看 `moto` 专属版式）、文章页、归档页、关于页、页脚备案号与二维码弹窗。

---

## 八、配置方式（首次在新环境启用时）

1. 主题放到 `wp-content/themes/yigeren-theme/` 后，后台「外观 → 主题」启用。
2. 「设置 → 固定链接」：建议选择**文章名** `/%postname%/`。
   > 注意：`front-page.php` 中分类入口写死为 `home_url('/life/')` 形式（根路径），
   > 而 WordPress 默认分类链接为 `/category/life/`。若线上分类 URL 带 `/category/` 前缀，
   > 首页入口链接会 404。**上线前需核对实际分类 URL 结构并统一。**
3. 「设置 → 阅读」：首页显示方式按需设置（`front-page.php` 存在时优先作为首页模板）。
4. 「文章 → 分类目录」：创建上表 5 个分类，**slug 必须完全一致**。
5. 「页面 → 新建页面」：创建 slug 为 `archive` 的页面并选用模板**归档页**；创建 slug 为 `about` 的页面。
6. 「外观 → 菜单」：创建菜单并分配到**主导航**（位置 `primary`）。
7. 「设置 → 常规」：站点标题用于页头 Logo 文字与页脚版权行（`bloginfo('name')`）。

---

## 九、源码同步脚本（`sync_theme.py`）

位置：`../AI生成文件/sync_theme.py`（仓库同级上层目录）。用途：SFTP 双向同步本地与服务器主题目录。

```bash
# 环境准备（仅需一次）
python -m venv .venv && ./.venv/Scripts/python.exe -m pip install paramiko

# 服务器 → 本地（拉取最新线上代码，默认行为）
python sync_theme.py

# 仅列出文件，不实际传输（预检）
python sync_theme.py --dry-run

# 本地 → 服务器（部署；会要求输入 yes 二次确认，跳过确认加 --yes）
python sync_theme.py --upload
```

脚本内的服务器地址、凭据、远端与本地路径均为脚本顶部常量，**换环境时改这里即可**。
上传具有**覆盖线上站点**的破坏性，脚本内置了二次确认；`--upload` 前请确保服务器已有备份。

> **🔒 安全红线**：该脚本顶部以明文常量保存服务器 root 密码与服务器 IP。
> 因此它**故意放置在仓库目录之外**（`../AI生成文件/`），**绝不可提交进本仓库或任何代码仓库**。
> 若需团队共享，应改为从环境变量或本地密钥文件读取凭据后再入库。

---

## 十、注意事项与已知问题

1. **⚠️ 凭据绝不可入库**：上层目录的 `一个人.txt` 含服务器 root 密码、宝塔账号、数据库口令。
   本仓库的 `.gitignore` 已屏蔽凭据类文件，但**该 txt 位于仓库之外，不受本仓库保护**，请勿复制进主题目录。
2. **内嵌 base64 图片（技术债）**：`footer.php` 与 `page-about.php` 各含一行约 **67.8 KB** 的
   `data:image/jpeg;base64,...`，这是两个文件体积异常（各约 70 KB）的唯一原因。
   建议迁移到媒体库后改为 URL 引用，可显著降低模板体积、提升可缓存性与可维护性。
3. **硬编码绝对域名**：`front-page.php` 的 Hero 背景图写死
   `https://yaoqiang.xin/wp-content/uploads/2026/06/homepage-banner.png`。
   换域名、搭 staging 或迁移时会直接失效，建议改为动态获取（媒体库或 `get_template_directory_uri()`）。
4. **域名记录不一致**：`style.css` 主题头 `Theme URI` 为 `https://yigeren.blog`，而实际站点与图片域名为
   `https://yaoqiang.xin`。**二者关系待确认**（可能为旧域名/未更新字段），不影响运行，但会误导接手者。
5. **换行符为 CRLF**：服务器上全部模板文件均为 CRLF（Windows 端编辑上传所致）。
   本仓库已设置 `core.autocrlf=false` 原样入库，**未做换行符规范化**，以保证回传服务器时不产生大面积 diff。
   在 Linux 环境下编辑时建议保持既有风格，避免混入混合换行。
6. **备份文件不入版本库**：`_backup_20260807/`、`*.bak`、`*.bak2` 等仍保留在本地磁盘，仅被忽略。
   如需纳入管理，删除 `.gitignore` 中对应规则即可。
7. **字体依赖第三方镜像**：字体走 `fonts.loli.net`（Google Fonts 国内镜像）。
   镜像不可用时排版会回退到系统字体，视觉观感会明显变化。
8. **无构建、无测试、无 CI**：改完即上传即生效，风险敞口直接暴露在线上。
   建议后续至少补充：staging 环境 + 提交前 `php -l` 语法检查。
9. **许可**：主题声明为 **GPL-2.0 or later**（WordPress 主题生态的强制要求）。
   分发本主题或基于其二次开发并分发时，须保持相同许可。

---

## 十一、元信息

| 项 | 值 |
| --- | --- |
| 主题名称 | 一个人的互联网笔记 |
| Text Domain | `yigeren` |
| 版本 | 1.0.0（V1.0 已上线并通过策划验收） |
| 作者 | Yizu（`style.css` 主题头记录） |
| 许可 | GNU GPL v2 or later |
| 入库文件数 | 11（含 `.gitignore`、`README.md`） |
| 源码来源 | 阿里云轻量应用服务器 `8.137.48.145`，SFTP 拉取 |
| 首版提交 | `d5a1a90` |
| 远端仓库 | `https://github.com/huogou/yigeren-theme`（已推送，**private**，2026-09-23 转私有） |
| 推送方式 | HTTPS + PAT（PAT 仅临时用于 `git push`，未写入任何配置，用完即弃） |

### 当前进度（2026-09-23）

- **V1.0 已上线并通过策划验收**：新增「折腾」分类 + 首页改版 + `/projects` 分类页 + 4 篇项目文章 + 项目 5 字段；
  上线前设计体检 4 项修复（导航毛玻璃、水印溢出、月份错显、摄影命名统一）均已落地。
- **下一阶段（策划主导）**：完善 4 个项目文章内容与项目素材；V1.1 补真实项目封面图。
- 详细交付与拍板记录见 `AI生成文件/给策划AI同步-开发交付.txt`。
