<?php

namespace App\Exports\Template;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Exports\Template\Variable;

class Style
{
  public const STYLE_BORDER_MAIN = [
    'borders' => [
      'outline' => [
        'borderStyle' => Border::BORDER_THIN,
        'color' => ['argb' => Variable::COLOR_BORDER],
      ],
    ],
  ];

  public const STYLE_TITLE_DOCUMENT = [
    'font' => [
      'name' => Variable::FONT_NAME_TITLE,
      'size' => Variable::FONT_SIZE_TITLE,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];
  
  public const STYLE_SUBTITLE_DOCUMENT = [
    'font' => [
      'name' => Variable::FONT_NAME_TITLE,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_LEFT,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_VALUE_HEADERS = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => false,
      'color' => [
        'argb' => Variable::COLOR_DATA_TEXT
      ],
    ],
    'borders' => [
      'bottom' => [
        'borderStyle' => Border::BORDER_THIN,
        'color' => ['argb' => Variable::COLOR_BORDER],
      ],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_LEFT,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_NAME_HEADERS = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
  ];

  public const STYLE_CARD_HEADERS = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
  ];

  public const STYLE_CARD_CUSTOMER = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_ADDRESS,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
  ];

  public const STYLE_HEADERS_DESCRIPTION = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => false,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_LEFT,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_HEADER_TABLE = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_HEADER_TABLE_TEXT],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'wrapText' => true,
    ],
    'fill' => [
      'fillType' => Fill::FILL_SOLID,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'borders' => [
      'allBorders' => [
        'borderStyle' => Border::BORDER_THIN,
        'color' => ['argb' => Variable::COLOR_BORDER],
      ],
    ],
  ];
  
  public const STYLE_BODY_TABLE = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_DATA,
      'bold' => false,
      'color' => [
        'argb' => Variable::COLOR_DATA_TEXT
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'wrapText' => true,
    ],
    'borders' => [
      'allBorders' => [
        'borderStyle' => Border::BORDER_THIN,
        'color' => ['argb' => Variable::COLOR_BORDER],
      ],
    ],
  ];

  public const STYLE_BODY_TABLE_NO_BORDER = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => (Variable::FONT_SIZE_DATA + 2),
      'bold' => true,
      'color' => [
        'argb' => Variable::COLOR_TITLE_TEXT
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
  ];

  public const STYLE_HEADER_CARD = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADER_CARD,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_HEADER_CARD_ERROR = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADER_CARD,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT_ERROR],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_VALUE_CARD = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_VALUE_CARD,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_VALUE_CARD_BODY = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_VALUE_CARD,
      'bold' => true,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_LEFT,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_TITLE_PLAIN = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => false,
      'color' => ['argb' => Variable::COLOR_TITLE_TEXT],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];

  public const STYLE_HEADER_TABLE_NO_BORDER = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => false,
      'color' => [
        'argb' => Variable::COLOR_DATA_TEXT
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER
    ],
  ];

  public const STYLE_HEADER_TABLE_NO_BORDER_PRIMARY = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => false,
      'color' => [
        'argb' => Variable::COLOR_TITLE_TEXT
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER
    ],
  ];

  public const STYLE_HEADER_TABLE_NO_BORDER_FOTTER = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => [
        'argb' => Variable::COLOR_DATA_TEXT_BOLD
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
      'horizontal' => Alignment::HORIZONTAL_CENTER
    ],
  ];

  public const STYLE_HEADERS_LIGHT = [
    'font' => [
      'name' => Variable::FONT_NAME_DATA,
      'size' => Variable::FONT_SIZE_HEADERS,
      'bold' => true,
      'color' => [
        'argb' => Variable::COLOR_DATA_TEXT
      ],
    ],
    'alignment' => [
      'horizontal' => Alignment::HORIZONTAL_CENTER,
      'vertical' => Alignment::VERTICAL_CENTER,
      'wrapText' => true,
    ],
  ];
}