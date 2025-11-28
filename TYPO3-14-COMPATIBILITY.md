# TYPO3 14 Compatibility for phpstan-typo3

This document describes the changes made to `saschaegerer/phpstan-typo3` for TYPO3 14
compatibility. This version supports **TYPO3 14 only**.

---

## Version Requirements

```json
{
    "require": {
        "typo3/cms-core": "^14.0",
        "typo3/cms-extbase": "^14.0"
    }
}
```

---

## Summary of Changes

| Category | Change | Reason |
|----------|--------|--------|
| Version | TYPO3 14 only | Breaking changes make dual-version support impractical |
| Request Attributes | Removed `backend.user` mapping | `FrontendBackendUserAuthentication` removed |
| Request Attributes | Removed `frontend.controller` mapping | `TypoScriptFrontendController` removed |
| Early Terminating | Removed `AbstractController` methods | Class removed in TYPO3 14 |
| Early Terminating | Removed `CommandController` methods | Class removed in TYPO3 14 |
| Stubs | Removed 3 redundant stub files | TYPO3 14 includes native PHPStan annotations |

---

## Request Attribute Mappings

The following request attribute mappings were removed because their corresponding
classes no longer exist in TYPO3 14:

### Removed: `backend.user`

```neon
# Removed from requestGetAttributeMapping:
backend.user: TYPO3\CMS\Backend\FrontendBackendUserAuthentication
```

**Breaking Change Reference:**
[Breaking-105377-DeprecatedFunctionalityRemoved.rst][breaking-105377]

### Removed: `frontend.controller`

```neon
# Removed from requestGetAttributeMapping:
frontend.controller: TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
```

**Breaking Change Reference:**
[Breaking-107831-RemovedTypoScriptFrontendController.rst][breaking-107831]

**Migration:** Use specific request attributes instead:

- `$request->getAttribute('frontend.page.information')` for page data
- `$request->getAttribute('frontend.typoscript')` for TypoScript
- `$request->getAttribute('frontend.user')` for frontend user

---

## Early Terminating Method Calls

The following early terminating method configurations were removed:

### Removed: `AbstractController`

```neon
# Removed from earlyTerminatingMethodCalls:
TYPO3\CMS\Extbase\Mvc\Controller\AbstractController:
    - redirectToUri
    - redirect
    - forward
    - throwStatus
```

**Reason:** `AbstractController` was removed in TYPO3 14. Use `ActionController`
directly.

### Removed: `CommandController`

```neon
# Removed from earlyTerminatingMethodCalls:
TYPO3\CMS\Extbase\Mvc\Controller\CommandController:
    - forward
    - quit
```

**Reason:** `CommandController` was removed. Use Symfony Console commands instead.

---

## Stub Files

### Removed Stubs

The following stub files were removed because TYPO3 14 now includes native PHPStan
annotations in these classes:

| Stub File | Reason for Removal |
|-----------|-------------------|
| `DomainObjectInterface.stub` | TYPO3 14 has complete interface definition |
| `RepositoryInterface.stub` | TYPO3 14 includes `@phpstan` annotations |
| `QueryResultInterface.stub` | TYPO3 14 includes `@phpstan` annotations |

### Retained Stubs

The following stubs are still required:

| Stub File | Purpose |
|-----------|---------|
| `ObjectStorage.stub` | Provides enhanced generic type information |
| `QueryInterface.stub` | Contains conditional return type for `execute()` |
| `Repository.stub` | Provides additional method type constraints |

---

## Remaining Configuration

### Current Context API Aspect Mappings

```neon
contextApiGetAspectMapping:
    backend.user: TYPO3\CMS\Core\Context\UserAspect
    date: TYPO3\CMS\Core\Context\DateTimeAspect
    fileProcessing: TYPO3\CMS\Core\Context\FileProcessingAspect
    frontend.preview: TYPO3\CMS\Core\Context\PreviewAspect
    frontend.user: TYPO3\CMS\Core\Context\UserAspect
    language: TYPO3\CMS\Core\Context\LanguageAspect
    security: TYPO3\CMS\Core\Context\SecurityAspect
    typoscript: TYPO3\CMS\Core\Context\TypoScriptAspect
    visibility: TYPO3\CMS\Core\Context\VisibilityAspect
    workspace: TYPO3\CMS\Core\Context\WorkspaceAspect
```

### Current Request Attribute Mappings

```neon
requestGetAttributeMapping:
    adminPanelRequestId: string
    applicationType: TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_*
    currentContentObject: TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
    extbase: TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters
    frontend.cache.collector: TYPO3\CMS\Core\Cache\CacheDataCollector
    frontend.cache.instruction: TYPO3\CMS\Frontend\Cache\CacheInstruction
    frontend.page.information: TYPO3\CMS\Frontend\Page\PageInformation
    frontend.typoscript: TYPO3\CMS\Core\TypoScript\FrontendTypoScript
    frontend.user: TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
    language: TYPO3\CMS\Core\Site\Entity\SiteLanguage
    module: TYPO3\CMS\Backend\Module\ModuleInterface
    moduleData: TYPO3\CMS\Backend\Module\ModuleData
    nonce: TYPO3\CMS\Core\Security\ContentSecurityPolicy\ConsumableNonce
    normalizedParams: TYPO3\CMS\Core\Http\NormalizedParams
    originalRequest: Psr\Http\Message\ServerRequestInterface
    routing: TYPO3\CMS\Core\Routing\SiteRouteResult|TYPO3\CMS\Core\Routing\PageArguments
    site: TYPO3\CMS\Core\Site\Entity\Site
    target: string
    typo3.testing.context: TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext
```

### Current Site Attribute Mappings

```neon
siteGetAttributeMapping:
    base: string
    baseVariants: list
    errorHandling: list
    languages: list
    rootPageId: int
    routeEnhancers: array
    settings: array
    websiteTitle: string
```

### Early Terminating Methods

```neon
earlyTerminatingMethodCalls:
    TYPO3\CMS\Extbase\Mvc\Controller\ActionController:
        - redirectToUri
        - redirect
        - forward
        - forwardToReferringRequest
        - throwStatus
    TYPO3\CMS\Core\Utility\HttpUtility:
        - redirect
        - setResponseCodeAndExit
    TYPO3\CMS\Form\Domain\Finishers\RedirectFinisher:
        - redirectToUri
    TYPO3\CMS\Extensionmanager\Utility\FileHandlingUtility:
        - sendZipFileToBrowserAndDelete
```

---

## TYPO3 14 Breaking Changes Reference

| Changelog | Affected Component |
|-----------|-------------------|
| Breaking-105377 | `FrontendBackendUserAuthentication`, magic repository methods |
| Breaking-106056 | `ValidatorInterface` (requires `setRequest()`/`getRequest()`) |
| Breaking-107229 | Annotations replaced by Attributes |
| Breaking-107831 | `TypoScriptFrontendController` removed |

---

## Testing

Run the PHPStan tests to verify compatibility:

```bash
ddev composer test
```

---

## Related Resources

- [TYPO3 14 Changelog][changelog-14]
- [PHPStan TYPO3 Repository][phpstan-typo3]

[breaking-105377]: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-105377-DeprecatedFunctionalityRemoved.html
[breaking-107831]: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-107831-RemovedTypoScriptFrontendController.html
[changelog-14]: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Index.html
[phpstan-typo3]: https://github.com/sascha-egerer/phpstan-typo3
