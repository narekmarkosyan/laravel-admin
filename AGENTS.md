# Repository instructions

## Deploy workflow

When the user's entire request is `deploy` (case-insensitive), perform this workflow without asking for an additional confirmation:

1. Treat every staged, unstaged, and untracked file in the repository as part of the proposed deployment. If there are no changes to commit, stop and report that there is nothing to deploy.
2. Review all proposed changes before staging them. Check for code-style violations, whitespace errors, syntax errors, logical errors, accidental debug code, secrets, and unrelated generated files. Use `git diff --check`, run every configured project lint or style check that applies, and run `php -l` on every changed PHP file. Do not silently fix or omit a failing file.
3. Run the database-independent test suite with `./vendor/bin/phpunit tests/Unit`. Do not check database connectivity and do not run tests that require or boot a database as part of this workflow.
4. If the review, syntax checks, style checks, or tests reveal any problem, stop immediately. Report the problem and do not stage, commit, tag, or push anything.
5. Fetch tags from `origin`, then identify the highest valid SemVer tag reachable from `HEAD`. Create the next patch version by incrementing only the final component (for example, `v1.9.0` becomes `v1.9.1`, while `1.9.0` becomes `1.9.1`). Preserve whether the existing tags use a `v` prefix. If there is no valid SemVer tag, or the tag format is ambiguous, stop and ask the user for the initial/desired version.
6. Generate a short, imperative commit message that accurately summarizes the reviewed changes. Stage all proposed changes and create one commit with that message.
7. Create an annotated tag for the computed version, using the version as the tag message.
8. Push the current branch and the new tag to `origin` atomically. If the push fails, report the failure without rewriting history, deleting tags, or force-pushing.
9. Report the commit hash, commit message, tag, test result, and push result.

Never use `--force`, `--force-with-lease`, skip hooks, or bypass a failed check during this workflow.
