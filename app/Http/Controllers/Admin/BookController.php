<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Services\AuditService;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly BookService $bookService,
    ) {}

    public function index(Request $request): View
    {
        $query = Book::query()->with('category');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('book_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->integer('category')) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('categories'));
    }

    public function store(StoreBookRequest $request, BookService $service): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover');
        }

        $book = $service->create($data);
        $this->audit->logCreate($book);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function update(UpdateBookRequest $request, Book $book, BookService $service): RedirectResponse
    {
        $data = $request->validated();
        $oldValues = $book->getAttributes();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover');
        } elseif ($request->boolean('_remove_cover')) {
            $data['_remove_cover'] = true;
        }

        $book = $service->update($book, $data);
        $this->audit->logUpdate($book, $oldValues, $book->getAttributes());

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book, BookService $service): RedirectResponse
    {
        $this->audit->logDelete($book);
        $service->delete($book);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus.');
    }

    public function show(Book $book): View
    {
        $book->load('category');
        $categories = Category::orderBy('name')->get();

        return view('admin.books.show', compact('book', 'categories'));
    }

    public function edit(Book $book): View
    {
        $book->load('category');
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function qrModal(Request $request, Book $book): JsonResponse
    {
        // Endpoint untuk mengambil data buku sebagai JSON (untuk modal QR)
        $book->load('category');

        return response()->json([
            'id' => $book->id,
            'book_code' => $book->book_code,
            'title' => $book->title,
            'isbn' => $book->isbn,
            'author' => $book->author,
            'rack_location' => $book->rack_location,
            'qr_code' => $book->qr_code,
            'qr_url' => $book->qr_code ? asset('storage/'.$book->qr_code) : null,
            'category_name' => $book->category?->name,
        ]);
    }

    public function regenerateQrCode(Book $book, BookService $service): RedirectResponse
    {
        $service->regenerateQrCode($book);

        return redirect()->back()->with('success', 'QR Code berhasil digenerate ulang.');
    }

    public function showQrCode(Book $book): View
    {
        $book->load('category');

        return view('admin.books.print-qr', compact('book'));
    }

    public function bulkGenerateQr(BookService $service): RedirectResponse
    {
        $books = Book::whereNull('qr_code')->orWhereRaw('qr_code = ""')->get();

        if ($books->isEmpty()) {
            return redirect()->back()->with('info', 'Semua buku sudah memiliki QR Code.');
        }

        $count = 0;
        foreach ($books as $book) {
            $service->generateQrCodeOnly($book);
            $count++;
        }

        return redirect()->back()->with('success', "QR Code berhasil digenerate untuk {$count} buku.");
    }

    public function bulkPrintQr(Request $request): View
    {
        $search = $request->input('search', '');
        $category = $request->input('category', '');

        $query = Book::with('category')
            ->whereNotNull('qr_code')
            ->whereRaw('qr_code != ""');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $books = $query->latest()->get();

        return view('admin.books.print-all-qr', compact('books'));
    }

    private function buildBulkQrHtml($books): string
    {
        $rows = '';
        foreach ($books as $book) {
            $qrUrl = asset('storage/'.$book->qr_code);
            $rows .= '<tr>
                <td>
                    <div style="text-align:center;page-break-inside:avoid;">
                        <img src="'.e($qrUrl).'" alt="QR" style="width:130px;height:130px;display:block;margin:0 auto;"/>
                        <div style="font-family:Bangers,sans-serif;font-size:11px;color:#aaa;margin-top:4px;text-transform:uppercase;letter-spacing:1px;">SCAN</div>
                    </div>
                </td>
                <td style="padding-left:12px;vertical-align:top;">
                    <div style="font-family:Bangers,sans-serif;font-size:16px;color:#1A1A2E;line-height:1.3;margin-bottom:8px;max-width:260px;">'.e($book->title).'</div>
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <div style="display:flex;gap:6px;">
                            <span style="font-family:Bangers,sans-serif;font-size:10px;color:#aaa;text-transform:uppercase;letter-spacing:1px;min-width:50px;">KODE</span>
                            <span style="font-family:Bangers,sans-serif;font-size:12px;color:#1A1A2E;font-weight:900;">'.e($book->book_code).'</span>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <span style="font-family:Bangers,sans-serif;font-size:10px;color:#aaa;text-transform:uppercase;letter-spacing:1px;min-width:50px;">ISBN</span>
                            <span style="font-family:Bangers,sans-serif;font-size:12px;color:#888;">'.e($book->isbn ?? '-').'</span>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <span style="font-family:Bangers,sans-serif;font-size:10px;color:#aaa;text-transform:uppercase;letter-spacing:1px;min-width:50px;">PENULIS</span>
                            <span style="font-family:Bangers,sans-serif;font-size:12px;color:#888;">'.e($book->author ?? '-').'</span>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <span style="font-family:Bangers,sans-serif;font-size:10px;color:#aaa;text-transform:uppercase;letter-spacing:1px;min-width:50px;">RAK</span>
                            <span style="font-family:Bangers,sans-serif;font-size:12px;color:#888;">'.e($book->rack_location ?? '-').'</span>
                        </div>
                    </div>
                </td>
            </tr>';
        }

        return '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Daftar QR Code Buku</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:sans-serif;padding:20px;background:#fff;color:#1A1A2E;}
h1{font-family:Bangers,sans-serif;font-size:20px;color:#1A1A2E;margin-bottom:6px;letter-spacing:1px;}
.sub{font-family:Bangers,sans-serif;font-size:12px;color:#aaa;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;}
table{width:100%;border-collapse:collapse;}
td{border:2px solid #1A1A2E;padding:14px 12px;background:#fff;vertical-align:top;}
.print-btn{background:#FF6B35;color:#fff;border:2px solid #1A1A2E;box-shadow:3px 3px 0 #1A1A2E;
          padding:10px 20px;font-family:Bangers,sans-serif;font-size:13px;letter-spacing:1px;cursor:pointer;margin-bottom:20px;}
@media print{body{padding:0;}.print-btn{display:none!important;}td{page-break-inside:avoid;border:2px solid #000;}}
</style>
</head>
<body>
<button class="print-btn" onclick="window.print()">🖨️ PRINT QR CODES</button>
<h1>📚 DAFTAR QR CODE BUKU</h1>
<div class="sub">Total: '.$books->count().' buku — Generated: '.now()->locale('id')->translatedFormat('d F Y, H:i').' WIB</div>
<table>
'.$rows.'
</table>
</body>
</html>';
    }

    public function lookupByCode(Request $request): JsonResponse
    {
        $book = Book::with('category')->where('book_code', $request->get('code'))->first();

        if (! $book) {
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        }

        if (! $book->isAvailable()) {
            return response()->json(['error' => 'Buku tidak tersedia untuk dipinjam'], 409);
        }

        return response()->json([
            'id' => $book->id,
            'book_code' => $book->book_code,
            'title' => $book->title,
            'category' => $book->category?->name,
            'author' => $book->author,
            'stock' => $book->stock,
        ]);
    }
}
