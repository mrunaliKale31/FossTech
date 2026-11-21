<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Tests\Unit\Git;

use OCA\GitIntegration\Git\GitProviderFactory;
use OCA\GitIntegration\Git\GitLabProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GitProviderFactoryTest extends TestCase {
    private GitProviderFactory $factory;

    protected function setUp(): void {
        parent::setUp();
        $this->factory = new GitProviderFactory(new NullLogger());
    }

    public function testCreateGitLabProvider(): void {
        $provider = $this->factory->create('gitlab', 'https://gitlab.com', 'test-token');
        $this->assertInstanceOf(GitLabProvider::class, $provider);
    }

    public function testCreateUnsupportedProvider(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create('unsupported', 'https://example.com', 'test-token');
    }

    public function testGetSupportedProviders(): void {
        $providers = $this->factory->getSupportedProviders();
        $this->assertIsArray($providers);
        $this->assertArrayHasKey('gitlab', $providers);
        $this->assertTrue($providers['gitlab']['implemented']);
    }
}
