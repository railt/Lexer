<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Lexer\Driver;

use Railt\Io\Readable;
use Railt\Lexer\Driver\Common\PCRECompiler;
use Railt\Lexer\Stateless;

/**
 * Class NativeStateless
 */
class NativeStateless extends NativeStateful implements Stateless
{
    /**
     * @var PCRECompiler
     */
    private $pcre;

    /**
     * NativeStateless constructor.
     */
    public function __construct()
    {
        $this->pcre = new PCRECompiler();
        parent::__construct('');
    }

    /**
     * @param Readable $input
     * @return \Traversable
     */
    public function lex(Readable $input): \Traversable
    {
        foreach (parent::exec($this->pcre->compile(), $input->getContents()) as $token) {
            if (! \in_array($token->name(), $this->skipped, true)) {
                yield $token;
            }
        }
    }

    /**
     * @param string $name
     * @return Stateless
     */
    public function skip(string $name): Stateless
    {
        $this->skipped[] = $name;

        return $this;
    }

    /**
     * @param string $name
     * @param string $pcre
     * @param bool $skip
     * @return Stateless
     */
    public function add(string $name, string $pcre, bool $skip = false): Stateless
    {
        $this->pcre->addToken($name, $pcre);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->pcre->getTokens());
    }

    /**
     * @return iterable
     */
    public function getDefinedTokens(): iterable
    {
        return $this->pcre->getTokens();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isSkipped(string $name): bool
    {
        return \in_array($name, $this->skipped, true);
    }
}
