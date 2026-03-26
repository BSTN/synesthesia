CREATE TABLE IF NOT EXISTS profile (
  UID TEXT PRIMARY KEY,
  created TEXT NOT NULL,
  modified TEXT NOT NULL,
  IP TEXT,
  language TEXT,
  finishedtests TEXT,
  touchscreen TEXT,
  USERID TEXT,
  SHARED TEXT
);

CREATE TABLE IF NOT EXISTS questions (
  ID INTEGER PRIMARY KEY AUTOINCREMENT,
  created TEXT NOT NULL,
  modified TEXT NOT NULL,
  IP TEXT,
  UID TEXT NOT NULL,
  testname TEXT,
  setname TEXT,
  symbol TEXT,
  value TEXT,
  clicks INTEGER,
  clicksslider INTEGER,
  position TEXT,
  timing INTEGER,
  qnr INTEGER
);

CREATE TABLE IF NOT EXISTS extra (
  UID TEXT PRIMARY KEY,
  created TEXT NOT NULL,
  modified TEXT NOT NULL,
  IP TEXT,
  data TEXT
);

CREATE TABLE IF NOT EXISTS access (
  IP TEXT PRIMARY KEY,
  created TEXT NOT NULL,
  modified TEXT NOT NULL,
  NUM INTEGER NOT NULL DEFAULT 0
);
