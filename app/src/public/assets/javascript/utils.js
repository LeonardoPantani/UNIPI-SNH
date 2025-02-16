const PASSWORD_MIN_LENGTH = 8;
const USERNAME_MIN_LENGTH = 5;
const USERNAME_MAX_LENGTH = 20;
const NOVEL_TITLE_MAX_LENGTH = 100;
const NOVEL_TEXT_MAX_LENGTH = 500;
const EMAIL_REGEX = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/;
const USERNAME_REGEX = new RegExp(`^[a-zA-Z0-9\-_]{${USERNAME_MIN_LENGTH},${USERNAME_MAX_LENGTH}}$`);