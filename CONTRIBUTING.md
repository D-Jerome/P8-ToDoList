# How to contribute

Please first read the readme.md file at the root of the project.
Then follow the contribution steps of this project.

## Report bugs or propose a new feature

You can report a bug or request the addition of a feature to the application by creating a pull request.

To do so, please respect these few rules:

-   Give a title as clear and short as possible.
-   Describe the problem clearly. In the case of a bug, give as many details as possible about your environment (OS, PHP version, extensions ...) and describe the steps to reproduce the bug.
-   Use the bug or feature label for your problem.

## The instructions

### Step 1: Fork the repository

### Step 2: Clone your fork

### Step 3: Install the project

Install the project by following the instructions in the readme.md file at the root of the project.

### Step 4: How to work on the project

1. Create a new branch. Be sure to give a short and explicit name that summarizes the purpose of the work that will be done on the branch.

2. Work on your branch.

3. Testing and test covers.
   The application is covered by unit and functional tests. You can find the coverage report in the coverage/index.html folder at the root. Implement your own tests and test your code.

4. Code quality.
   Before committing your code, check the quality of your code.

5. Commit your code.
   `git commit -am 'an explicit commit description'`

6. Push your branch.
   `git push origin your-branch-name`

### Step 5. Create a merge request

Access your fork on your Github account to open a merge request. Provide a clear title and concise explanation of your work in the description of your merge request. Add a problem reference.

Now that you're sure of your work, you can click "Create Merge Request" and target the project "branch".

Github will automatically run tests and the code will be checked with Codacy. Your code must pass the syntax and testing stage to have a chance to be merged.

If your pull request conforms to the project's contribution recommendations, your feature or fix will be integrated into the application.

## Standards to be respected

-   PHP Standards Recommendations (PSR) <https://www.php-fig.org/psr/>
    -   PSR-1
    -   PSR-12
    -   PSR-4
-   Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html#making-your-code-follow-the-coding-standards>

## The Best Practices

-   Symfony Best Practices <https://symfony.com/doc/current/best_practices.html#creating-the-project>
-   Doctrine Best Practices <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/best-practices.html>

## Tests

-   PHPUnit documentation: <https://phpunit.de/documentation.html>
