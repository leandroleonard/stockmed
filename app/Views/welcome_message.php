<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockmed - Gestão Inteligente para Farmácias</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f7f9fa;
            color: #222;
        }
        .header {
            background: rgb(24, 144, 255);
            color: #fff;
            padding: 40px 0 30px 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.8rem;
            letter-spacing: 2px;
        }
        .header p {
            margin-top: 15px;
            font-size: 1.2rem;
        }
        .cta-btn {
            background: #fff;
            color: rgb(24, 144, 255);
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 30px;
            margin-top: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s, color 0.2s;
        }
        .cta-btn:hover {
            background: rgb(2, 91, 175);
            color: #fff;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 50px 0;
            gap: 30px;
        }
        .feature {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.08);
            padding: 30px 25px;
            max-width: 320px;
            text-align: center;
        }
        .feature h3 {
            color: rgb(24, 144, 255);
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            padding: 25px 0;
            background: #e0e0e0;
            color: #555;
            font-size: 0.95rem;
        }
        @media (max-width: 900px) {
            .features {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Stockmed</h1>
        <p>Simplifique a gestão da sua farmácia com tecnologia, segurança e praticidade.</p>
        <a href="/login"><button class="cta-btn">Experimente Agora</button></a>
    </div>

    <div class="features">
        <div class="feature">
            <h3>Gestão de Estoque</h3>
            <p>Controle total de produtos, validade, entradas e saídas. Evite rupturas e desperdícios.</p>
        </div>
        <div class="feature">
            <h3>Vendas e Compras</h3>
            <p>Registre vendas, compras e acompanhe o desempenho do seu negócio em tempo real.</p>
        </div>
        <div class="feature">
            <h3>Clientes e Fornecedores</h3>
            <p>Gerencie o cadastro de clientes e fornecedores de forma simples e eficiente.</p>
        </div>
    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> Stockmed. Todos os direitos reservados.
    </div>
</body>
</html>