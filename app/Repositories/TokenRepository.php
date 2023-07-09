<?php
/**
 * Token repository.
 */

namespace App\Repositories;
use App\Repositories\Interfaces\TokenRepositoryInterface;
use Carbon\Carbon;

/**
 * Token repository.
 */
class TokenRepository implements TokenRepositoryInterface
{
    /**
     * TranzakProduct.
     *
     * @var string
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param string $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $token_attributes)
    {
        $token_attributes['token_type'] = 'Bearer';
        $token_attributes['product'] = $this->product;
        $token_attributes['access_token'] = $token_attributes['token'];
        if (isset($token_attributes['expiresIn'])) {
            $token_attributes['expires_at'] = Carbon::now()->addSeconds($token_attributes['expiresIn']);
        }

        return Token::create($token_attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveAll()
    {
        return Token::where('product', $this->product)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve($access_token = null)
    {
        if ($access_token) {
            return Token::query()
                ->where('access_token', $access_token)
                ->where('product', $this->product)
                ->first();
        }

        return Token::query()
            ->where('product', $this->product)
            ->latest('created_at')
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($access_token, array $attributes)
    {
        $token = Token::query()
            ->where('access_token', $access_token)
            ->where('product', $this->product)
            ->first();

        $token->update($attributes);

        return $token->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($access_token)
    {
        Token::query()
            ->where('access_token', $access_token)
            ->where('product', $this->product)
            ->delete();
    }
}
