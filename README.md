tippingtrees
============

Repository for the Tipping Trees, LLC free, open source, secure, and private social network and web identities

Zero Knowledge Design
Other companies can encrypt your data. Tipping Trees encrypts your data AND your connections. Your information is encrypted and spread between multiple database tables to obfuscate your data until you log in and request it.

Universal and Seamless Cryptography

Public Key (RSA)
Upon registration, a 2048 bit RSA key pair is generated locally on YOUR computer. A verification email links your crypto key with your email. Your private key is encrypted with your account password.

Advanced Encryption Standard (AES)
Most entities on Tipping Trees are encrypted with 128-bit AES. The AES key is RSA-encrypted with the public key of each relevant user (each member of a group, conversation, or sender and receiver of a private message).

Seeding the Pseudo-Random Number Generator (PRNG)
The PRNG used to generate all encryption keys on your computer (never on the server) are seeded directly from Random.org to your computer via a secure SSL tunnel.

Your password NEVER touches the server
Your password is used to verify your identity and to decrypt your RSA private key. When you login, your password is first encrypted with SHA-512 (Secure Hash Algorithm using 512 bits) locally on your computer, then the result is sent via an SSL-secured connection to the Tipping Trees server. The hashed password is salted then hashed (SHA-512) on the server, and authenticated. Upon authentication your stored encrypted private RSA key is sent to your computer. Your login also encrypted your password (AES) with a 512-bit random session key and stored on your machine. When need to decrypt or sign any entity on Tipping Trees your session key, encrypted password, and encrypted RSA key are used to temporarily obtain your private RSA key.

Groups
Tipping Trees implements the open source cryptography behind the scenes. You can see hashes and keys if you want (via mouseovers and green VERIFIED or red UNVERIFIED markers) but Tipping Trees enables you to create and join secure groups with friends and associates with a high degree of confidence that your collaboration and discussion is completely private.

Web of Trust
As with any public key infrastructure, you trust the identities of those you know. You can sign the accounts of people you know, so that your friends and others can see and verify the signature which may enhance their trust as well.

