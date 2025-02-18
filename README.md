# Tooling to Clone Prismic Repositories

This is a project that you should clone and run in an environment where you have PHP 8.4 installed.

It's not fancy, but it seems to work OK.

It's also still WIP…

## Things that definitely don't work

Cloning multiple locales hasn't been implemented. I currently have no need for it, so it's unlikely to be implemented unless someone else does it.

## Usage

### Clone

You know how to do this already right?

### Setup Environment

[Create a new repository at prismic.io](https://prismic.io/dashboard/new-repository) and go through the initial on-boarding until you can set the master locale. Use a master locale that matches the source locale, however, you can override the locale to match the target _(blindly)_ if you are confident that'd be OK. For example, copying from `en-us` to `en-gb` or vice versa would be fine. Look at [`./config/autoload/repository.global.php`](./config/autoload/repository.global.php) for more details.

Copy the [`./example.env.dist`](./example.env.dist) file to `./.env` and fill out the relevant details. You only need to supply _read_ tokens if your repository requires them. _Write_ tokens are mandatory for both source and target, but no writes happen on the source.

On your **source** repo, make sure that any documents you want to be cloned have been published. Un-published docs, or docs published to releases will not be copied.

### Run

```bash
php bin/run.php
```

## What happens

- _All_ assets are copied from source to target, not just the ones that are used in your documents. If an error occurs during asset copying, You can re-try safely. You won't end up with duplicated assets.
- _All_ document type definitions are cloned from source to target along with any shared slices. Again, these are tracked, so can be retried if any errors occur during transfer.
- _All_ source documents are downloaded at the **master ref**.
- Works through source documents one at a time, adjusting image identifiers and sends the data to the target migration release.
- TODO - Once all documents are processed, adjust all internal document links to point at the new equivalent document.
