-- スキマパーク データベース初期化スクリプト
-- テーブル一覧.txt の仕様書に完全準拠
-- Docker起動時に自動実行されます

-- =============================================
-- 1. 会員テーブル (MEMBER_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS MEMBER_TABLE (
    USER_ID INT AUTO_INCREMENT NOT NULL COMMENT '会員ID: 数字(自動連番)',
    EMAIL VARCHAR(1024) NOT NULL COMMENT 'メールアドレス',
    PASSWORD VARCHAR(64) NOT NULL COMMENT 'パスワード: 英数混合8文字以上20以下',
    TEL VARCHAR(64) NOT NULL COMMENT '電話番号: XXX-XXXX-XXXX等',
    BIRTH DATE NOT NULL COMMENT '生年月日: YYYY/MM/DD',
    SHOW_BIRTH BOOLEAN NOT NULL DEFAULT FALSE COMMENT '生年月日の公開設定: true:公開, false:非公開',
    GENDER INT NOT NULL COMMENT '性別: 0:男性, 1:女性, 2:その他',
    SHOW_GENDER BOOLEAN NOT NULL DEFAULT FALSE COMMENT '性別の公開設定: true:公開, false:非公開',
    IDENTITY VARCHAR(1024) NOT NULL COMMENT '本人確認書類: 画像の格納先の情報',
    USERNAME VARCHAR(128) NOT NULL COMMENT 'ユーザ名',
    PRIMARY KEY (USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会員テーブル';

-- =============================================
-- 2. 土地テーブル (LAND_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS LAND_TABLE (
    LAND_ID INT AUTO_INCREMENT NOT NULL COMMENT '土地ID: 数字11桁(自動連番)',
    NAME VARCHAR(128) NOT NULL COMMENT '土地名: 40文字以下',
    PEREFECTURES INT NOT NULL COMMENT '都道府県: 0:北海道～',
    CITY VARCHAR(256) NOT NULL COMMENT '市区町村: 50字制限',
    STREET_ADDRESS VARCHAR(256) NOT NULL COMMENT '番地: 50字制限',
    AREA DECIMAL(5,2) NOT NULL COMMENT '面積: DECIMAL(5,2)',
    USER_ID INT NOT NULL COMMENT '所有者ID: 外部キー',
    PRIMARY KEY (LAND_ID),
    FOREIGN KEY (USER_ID) REFERENCES MEMBER_TABLE(USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='土地テーブル';

-- =============================================
-- 3. 貸し出し記録テーブル (RENTAL_RECORD_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS RENTAL_RECORD_TABLE (
    RECORD_ID INT AUTO_INCREMENT NOT NULL COMMENT '貸し出し記録ID: 自動連番',
    PRICE INT NOT NULL COMMENT '単価',
    PRICE_UNIT INT NOT NULL COMMENT '単価単位: 0:日あたり, 1:時間あたり, 2:15分あたり',
    RENTAL_START_DATE DATE NOT NULL COMMENT '貸し出し開始期間: YYYY/MM/DD',
    RENTAL_END_DATE DATE NOT NULL COMMENT '貸し出し終了期間: YYYY/MM/DD',
    RENTAL_START_TIME TIME NOT NULL COMMENT '貸し出し開始時間: hh/mm',
    RENTAL_END_TIME TIME NOT NULL COMMENT '貸し出し終了時間: hh/mm',
    LAND_ID INT NOT NULL COMMENT '土地ID: 外部キー',
    USER_ID INT NOT NULL COMMENT '会員ID: 外部キー',
    PRIMARY KEY (RECORD_ID),
    FOREIGN KEY (LAND_ID) REFERENCES LAND_TABLE(LAND_ID),
    FOREIGN KEY (USER_ID) REFERENCES MEMBER_TABLE(USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='貸し出し記録テーブル';

-- =============================================
-- 4. レビュー・コメントテーブル (REVIEW_COMMENT_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS REVIEW_COMMENT_TABLE (
    REVIEW_COMMENT_ID INT AUTO_INCREMENT NOT NULL COMMENT 'レビュー・コメントID: 数字11桁（自動連番）',
    LAND_REVIEW INT NOT NULL COMMENT '土地レビュー: 星1~5',
    LAND_COMMENT VARCHAR(512) DEFAULT NULL COMMENT '土地コメント: 150文字',
    USER_REVIEW INT NOT NULL COMMENT 'ユーザレビュー: 星1~5',
    USER_COMMENT VARCHAR(512) DEFAULT NULL COMMENT 'ユーザコメント: 150文字',
    DATE DATE NOT NULL COMMENT '日付: YYYY/MM/DD',
    USER_ID INT NOT NULL COMMENT '会員ID: 外部キー',
    LAND_ID INT NOT NULL COMMENT '土地ID: 外部キー',
    RECORD_ID INT NOT NULL COMMENT '貸し出し記録ID: 外部キー',
    PRIMARY KEY (REVIEW_COMMENT_ID),
    FOREIGN KEY (USER_ID) REFERENCES MEMBER_TABLE(USER_ID),
    FOREIGN KEY (LAND_ID) REFERENCES LAND_TABLE(LAND_ID),
    FOREIGN KEY (RECORD_ID) REFERENCES RENTAL_RECORD_TABLE(RECORD_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='レビュー・コメントテーブル';

-- =============================================
-- 5. 問い合わせテーブル (CONTACT_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS CONTACT_TABLE (
    CONTACT_ID INT AUTO_INCREMENT NOT NULL COMMENT '問い合わせID: 数字11桁(自動連番)',
    TITLE VARCHAR(128) NOT NULL COMMENT '主題: 40字以下',
    MESSAGE VARCHAR(1024) NOT NULL COMMENT '本文: 300字以下',
    USER_ID INT NOT NULL COMMENT '会員ID: 外部キー',
    DATE DATE NOT NULL COMMENT '日付: YYYY/MM/DD',
    STATUS INT NOT NULL DEFAULT 0 COMMENT 'ステータス: 未対応:0, 対応中:1, 対応済み:2',
    PRIMARY KEY (CONTACT_ID),
    FOREIGN KEY (USER_ID) REFERENCES MEMBER_TABLE(USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='問い合わせテーブル';

-- =============================================
-- 6. 返信テーブル (REPLY_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS REPLY_TABLE (
    REPLY_ID INT AUTO_INCREMENT NOT NULL COMMENT '返信ID: 数字連番',
    CONTACT_ID INT NOT NULL COMMENT '問い合わせID: 外部キー',
    USER_ID INT NOT NULL COMMENT '会員ID: 外部キー',
    MESSAGE VARCHAR(1024) NOT NULL COMMENT 'メッセージ: 最大300文字',
    DATE DATE NOT NULL COMMENT '日付: YYYY/MM/DD',
    PRIMARY KEY (REPLY_ID),
    FOREIGN KEY (CONTACT_ID) REFERENCES CONTACT_TABLE(CONTACT_ID),
    FOREIGN KEY (USER_ID) REFERENCES MEMBER_TABLE(USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='返信テーブル';

-- =============================================
-- 7. 連絡テーブル (CHAT_TABLE)
-- =============================================
CREATE TABLE IF NOT EXISTS CHAT_TABLE (
    CHAT_ID INT AUTO_INCREMENT NOT NULL COMMENT '連絡ID: 数字4桁(自動連番)',
    USER_ID_FROM INT NOT NULL COMMENT '会員ID_連絡元: 外部キー',
    USER_ID_TO INT NOT NULL COMMENT '会員ID_連絡先: 外部キー',
    MESSAGE VARCHAR(512) NOT NULL COMMENT 'メッセージ: 120字以内',
    IMAGE VARCHAR(2048) DEFAULT NULL COMMENT '画像: 画像の格納先の情報url',
    YEAR DATE NOT NULL COMMENT '西暦: YYYY (仕様書通りDATE型)',
    DATE DATE NOT NULL COMMENT '日付: MM/DD (仕様書通りDATE型)',
    TIME TIME NOT NULL COMMENT '時間: hh/mm/ss',
    PRIMARY KEY (CHAT_ID),
    FOREIGN KEY (USER_ID_FROM) REFERENCES MEMBER_TABLE(USER_ID),
    FOREIGN KEY (USER_ID_TO) REFERENCES MEMBER_TABLE(USER_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='連絡テーブル';

-- =============================================
-- Laravel用テーブル
-- =============================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
