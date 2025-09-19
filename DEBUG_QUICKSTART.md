# Debug Features Quick Start

## Enable Debugging

Add this to your `.env` file:
```env
APP_DEBUG=true
```

That's it! No other environment variables needed.

## Test Debug Endpoints

Try these URLs:

```bash
# Query logging example
curl "http://localhost:7879/debug/query"

# Manual profiling example  
curl "http://localhost:7879/debug/profiling"

# Exception tracking example
curl "http://localhost:7879/debug/exception"

# Slow query detection
curl "http://localhost:7879/debug/slow-query"

# Debug metrics (shows configuration status)
curl "http://localhost:7879/debug/info"
```

## Debug Output

JSON responses will include a `debug` section with:
- Query timing and count
- Memory usage
- Performance warnings 
- Exception details
- Profiling spans

## Troubleshooting

If debug endpoints return "Method not allowed":
- Make sure `app.env` is set to `local` in your config
- Check that you've updated the baseapi vendor package

If debug features aren't working:
- Set `APP_DEBUG=true` in your `.env`
- Restart your server after changing `.env`
- Use the `/debug/info` endpoint to check configuration status
