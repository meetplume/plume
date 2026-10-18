# Troubleshooting

When things go wrong, check the logs first.

![diagram](images/flow.png)

> Tip: enable debug mode in your `.env`.

:::tip[Read the logs]
Laravel writes every error to `storage/logs`.
:::

::: warning Stale config
Clear the config cache after editing `.env`.
:::

> [!NOTE]
> Queue workers keep old code in memory.

::: details
Restart them after each deploy.
:::

~~~md
## Example heading

:::danger
Never indexed.
:::
~~~

````md
```php title="routes/web.php"
return view('welcome');
```
````
