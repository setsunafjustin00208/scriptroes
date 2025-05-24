<?php

namespace App\Libraries;

class StructuredData
{
    private $context = 'https://schema.org';
    private $data = [];
    private $title = '';
    private $description = '';
    private $keywords = '';
    private $baseUrl = '';
    private $logoUrl = '';
    private $organizationName = '';
    private $language = 'en-US';

    /**
     * StructuredData constructor.
     * @param array $params Optional parameters to initialize the class properties.
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function setContext(string $context) { $this->context = $context; }
    public function setBaseUrl(string $url) { $this->baseUrl = $url; }
    public function setOrganizationName(string $name) { $this->organizationName = $name; }
    public function setLogoUrl(string $url) { $this->logoUrl = $url; }
    public function setTitle(string $title) { $this->title = $title; }
    public function setDescription(string $desc) { $this->description = $desc; }
    public function setKeywords(string $keywords) { $this->keywords = $keywords; }
    public function setLanguage(string $lang) { $this->language = $lang; }

    public function addProperty(string $key, $value) { $this->data[$key] = $value; }
    public function addProperties(array $properties) { $this->data = array_merge($this->data, $properties); }

    public function generate(): string
    {
        $structuredData = array_merge([
            '@context' => $this->context,
        ], $this->data);
        return '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . '</script>';
    }

    public function output() { echo $this->generate(); }

    public function getDefaultStructuredByType(string $type): array
    {
        $type = strtolower($type);
        $map = [
            'webpage' => $this->getWebPageStructuredData(),
            'newsarticle' => $this->getNewsArticleStructuredData(),
            'collectionpage' => $this->getCollectionPageStructuredData(),
            'breadcrumblist' => $this->getBreadcrumbListStructuredData(),
            'website' => $this->getWebsiteStructuredData(),
            'organization' => $this->getOrganizationStructuredData(),
        ];
        return $map[$type] ?? [];
    }

    private function getWebPageStructuredData(): array
    {
        return [
            '@type' => 'WebPage',
            '@id' => $this->baseUrl,
            'url' => $this->baseUrl,
            'name' => $this->title,
            'isPartOf' => ['@id' => $this->baseUrl . '#website'],
            'about' => ['@id' => $this->baseUrl . '#organization'],
            'description' => $this->description,
            'keywords' => $this->keywords,
            'breadcrumb' => ['@id' => $this->baseUrl . '#breadcrumb'],
            'inLanguage' => $this->language,
            'potentialAction' => [
                '@type' => 'ReadAction',
                'target' => [$this->baseUrl],
            ],
        ];
    }

    private function getNewsArticleStructuredData(): array
    {
        return [
            '@type' => 'NewsArticle',
            'mainEntityOfPage' => [
                '@id' => $this->baseUrl,
                '@type' => 'WebPage',
            ],
            'publisher' => $this->getOrganizationStructuredData(),
            'url' => $this->baseUrl,
            'headline' => $this->title,
            'isPartOf' => ['@id' => $this->baseUrl . '#website'],
            'description' => $this->description,
            'keywords' => $this->keywords,
            'datePublished' => '',
            'dateModified' => '',
            'articleBody' => '',
            'articleSection' => '',
            'potentialAction' => [
                '@type' => 'ReadAction',
                'target' => [$this->baseUrl],
            ],
        ];
    }

    private function getCollectionPageStructuredData(): array
    {
        return [
            '@type' => 'CollectionPage',
            '@id' => $this->baseUrl,
            'url' => $this->baseUrl,
            'name' => $this->title,
            'isPartOf' => ['@id' => $this->baseUrl . '#website'],
            'description' => $this->description,
            'keywords' => $this->keywords,
            'breadcrumb' => ['@id' => $this->baseUrl . '#breadcrumb'],
            'inLanguage' => $this->language,
            'potentialAction' => [
                '@type' => 'ReadAction',
                'target' => [$this->baseUrl],
            ],
        ];
    }

    private function getBreadcrumbListStructuredData(): array
    {
        return [
            '@type' => 'BreadcrumbList',
            '@id' => $this->baseUrl . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => $this->title,
                ],
            ],
        ];
    }

    private function getWebsiteStructuredData(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => $this->baseUrl . '#website',
            'url' => $this->baseUrl,
            'name' => $this->title,
            'description' => $this->description,
            'potentialAction' => [
                [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => $this->baseUrl . 'search/#gsc.q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            'inLanguage' => $this->language,
        ];
    }

    private function getOrganizationStructuredData(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => $this->baseUrl . '#organization',
            'name' => $this->organizationName,
            'url' => $this->baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'inLanguage' => $this->language,
                '@id' => $this->logoUrl,
                'url' => $this->logoUrl,
                'contentUrl' => $this->logoUrl,
                'width' => 246,
                'height' => 158,
                'caption' => $this->organizationName,
            ],
            'image' => ['@id' => $this->logoUrl],
            'sameAs' => [],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => '',
                'addressRegion' => '',
                'postalCode' => '',
                'addressCountry' => '',
            ],
        ];
    }
}
