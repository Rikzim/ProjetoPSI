-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: maislusitania-db
-- Generation Time: Mar 12, 2026 at 04:26 PM
-- Server version: 5.7.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yii2advanced`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '8', 1773332624),
('gestor', '30', 1773332700),
('user', '10', 1773332654);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('accessBackoffice', 2, 'Aceder ao back-office', NULL, NULL, 1766503979, 1766503979),
('addBilling', 2, 'Adicionar bilhetes', NULL, NULL, 1766503979, 1766503979),
('addDistrict', 2, 'Adicionar distritos', NULL, NULL, 1766503979, 1766503979),
('addEvent', 2, 'Adicionar eventos', NULL, NULL, 1766503979, 1766503979),
('addFavorite', 2, 'Adicionar aos favoritos', NULL, NULL, 1766503979, 1766503979),
('addNews', 2, 'Adicionar notícias', NULL, NULL, 1766503979, 1766503979),
('addPlace', 2, 'Adicionar locais', NULL, NULL, 1766503979, 1766503979),
('addReview', 2, 'Adicionar avaliações e comentários', NULL, NULL, 1766503979, 1766503979),
('addTypePlace', 2, 'Adicionar tipos de locais', NULL, NULL, 1766503979, 1766503979),
('addUser', 2, 'Adicionar utilizadores', NULL, NULL, 1766503979, 1766503979),
('admin', 1, 'Administrador - Acesso total ao sistema', NULL, NULL, 1766497843, 1766497843),
('buyTickets', 2, 'Adquirir bilhetes online', NULL, NULL, 1766503979, 1766503979),
('cancelOwnReservation', 2, 'Cancelar próprias reservas', NULL, NULL, 1766503979, 1766503979),
('deleteAnyReview', 2, 'Soft Delete de qualquer avaliação', NULL, NULL, 1766503979, 1766503979),
('deleteBilling', 2, 'Eliminar bilhetes', NULL, NULL, 1766503979, 1766503979),
('deleteDistrict', 2, 'Eliminar distritos', NULL, NULL, 1766503979, 1766503979),
('deleteEvent', 2, 'Eliminar eventos', NULL, NULL, 1766503979, 1766503979),
('deleteNews', 2, 'Eliminar notícias', NULL, NULL, 1766503979, 1766503979),
('deleteOwnProfile', 2, 'Eliminar próprio perfil', NULL, NULL, 1766503979, 1766503979),
('deleteOwnReview', 2, 'Eliminar próprias avaliações', NULL, NULL, 1766503979, 1766503979),
('deletePlace', 2, 'Eliminar locais', NULL, NULL, 1766503979, 1766503979),
('deleteReservations', 2, 'Eliminar reservas', NULL, NULL, 1766503979, 1766503979),
('deleteTypePlace', 2, 'Eliminar tipos de locais', NULL, NULL, 1766503979, 1766503979),
('deleteUser', 2, 'Eliminar utilizadores', NULL, NULL, 1766503979, 1766503979),
('editBilling', 2, 'Editar bilhetes', NULL, NULL, 1766503979, 1766503979),
('editDistrict', 2, 'Editar distritos', NULL, NULL, 1766503979, 1766503979),
('editEvent', 2, 'Editar eventos', NULL, NULL, 1766503979, 1766503979),
('editNews', 2, 'Editar notícias', NULL, NULL, 1766503979, 1766503979),
('editOwnProfile', 2, 'Editar próprio perfil via API', NULL, NULL, 1766503979, 1766503979),
('editOwnReview', 2, 'Editar próprias avaliações', NULL, NULL, 1766503979, 1766503979),
('editPlace', 2, 'Editar locais', NULL, NULL, 1766503979, 1766503979),
('editProfile', 2, 'Editar próprio perfil', NULL, NULL, 1766503979, 1766503979),
('editReservations', 2, 'Editar reservas', NULL, NULL, 1766503979, 1766503979),
('editReview', 2, 'Editar avaliações e comentários', NULL, NULL, 1766503979, 1766503979),
('editTypePlace', 2, 'Editar tipos de locais', NULL, NULL, 1766503979, 1766503979),
('editUser', 2, 'Editar utilizadores', NULL, NULL, 1766503979, 1766503979),
('gestor', 1, 'Gestor - Acesso ao back-office para gestão de conteúdos', NULL, NULL, 1766497843, 1766497843),
('removeFavorite', 2, 'Remover dos favoritos', NULL, NULL, 1766503979, 1766503979),
('user', 1, 'Utilizador autenticado - Acesso completo ao front-office', NULL, NULL, 1766497843, 1766497843),
('viewBilling', 2, 'Visualizar bilhetes', NULL, NULL, 1766503979, 1766503979),
('viewDistrict', 2, 'Visualizar distritos', NULL, NULL, 1766503979, 1766503979),
('viewEvent', 2, 'Visualizar eventos', NULL, NULL, 1766503979, 1766503979),
('viewFavorites', 2, 'Visualizar lista de favoritos', NULL, NULL, 1766503979, 1766503979),
('viewNews', 2, 'Visualizar notícias', NULL, NULL, 1766503979, 1766503979),
('viewOwnProfile', 2, 'Visualizar próprio perfil', NULL, NULL, 1766503979, 1766503979),
('viewPlace', 2, 'Visualizar locais', NULL, NULL, 1766503979, 1766503979),
('viewReservations', 2, 'Visualizar reservas', NULL, NULL, 1766503979, 1766503979),
('viewReviews', 2, 'Visualizar avaliações e comentários', NULL, NULL, 1766503979, 1766503979),
('viewTypePlace', 2, 'Visualizar tipos de locais', NULL, NULL, 1766503979, 1766503979),
('viewUser', 2, 'Visualizar utilizadores', NULL, NULL, 1766503979, 1766503979);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('gestor', 'accessBackoffice'),
('gestor', 'addBilling'),
('admin', 'addDistrict'),
('gestor', 'addEvent'),
('user', 'addFavorite'),
('gestor', 'addNews'),
('gestor', 'addPlace'),
('user', 'addReview'),
('gestor', 'addTypePlace'),
('admin', 'addUser'),
('user', 'buyTickets'),
('user', 'cancelOwnReservation'),
('gestor', 'deleteAnyReview'),
('gestor', 'deleteBilling'),
('admin', 'deleteDistrict'),
('gestor', 'deleteEvent'),
('gestor', 'deleteNews'),
('user', 'deleteOwnProfile'),
('user', 'deleteOwnReview'),
('gestor', 'deletePlace'),
('gestor', 'deleteReservations'),
('gestor', 'deleteTypePlace'),
('admin', 'deleteUser'),
('gestor', 'editBilling'),
('admin', 'editDistrict'),
('gestor', 'editEvent'),
('gestor', 'editNews'),
('user', 'editOwnProfile'),
('user', 'editOwnReview'),
('gestor', 'editPlace'),
('gestor', 'editReservations'),
('gestor', 'editTypePlace'),
('admin', 'editUser'),
('admin', 'gestor'),
('user', 'removeFavorite'),
('gestor', 'user'),
('gestor', 'viewBilling'),
('admin', 'viewDistrict'),
('user', 'viewEvent'),
('user', 'viewFavorites'),
('user', 'viewNews'),
('user', 'viewOwnProfile'),
('user', 'viewPlace'),
('user', 'viewReservations'),
('admin', 'viewUser');

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avaliacao`
--

CREATE TABLE `avaliacao` (
  `id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `classificacao` int(11) NOT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `data_avaliacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ativo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distrito`
--

CREATE TABLE `distrito` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `distrito`
--

INSERT INTO `distrito` (`id`, `nome`, `codigo`) VALUES
(1, 'Aveiro', 'AVR'),
(2, 'Beja', 'BJA'),
(3, 'Braga', 'BRG'),
(4, 'Bragança', 'BGC'),
(5, 'Castelo Branco', 'CBR'),
(6, 'Coimbra', 'CMB'),
(7, 'Évora', 'EVR'),
(8, 'Faro', 'FAR'),
(9, 'Guarda', 'GRD'),
(10, 'Leiria', 'LRA'),
(11, 'Lisboa', 'LSB'),
(12, 'Portalegre', 'PTG'),
(13, 'Porto', 'PRT'),
(14, 'Santarém', 'STR'),
(15, 'Setúbal', 'STB'),
(16, 'Viana do Castelo', 'VCT'),
(17, 'Vila Real', 'VRL'),
(18, 'Viseu', 'VSE'),
(19, 'Região Autónoma dos Açores', 'RAA'),
(20, 'Região Autónoma da Madeira', 'RAM');

-- --------------------------------------------------------

--
-- Table structure for table `evento`
--

CREATE TABLE `evento` (
  `id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evento`
--

INSERT INTO `evento` (`id`, `local_id`, `titulo`, `descricao`, `data_inicio`, `data_fim`, `imagem`, `ativo`) VALUES
(2, 1, 'Reinauguração do Museu', 'As portas do Museu Nacional de Arte Antiga voltam a abrir-se para celebrar o reencontro entre o público e os maiores tesouros da nossa cultura. Após um período de renovação, convidamo-lo a explorar espaços revitalizados e a olhar para as nossas obras-primas com uma nova luz.\r\n\r\nVenha fazer parte deste momento histórico e perca-se na beleza de séculos de arte, agora numa experiência ainda mais imersiva e acolhedora. A arte é eterna, e a sua visita torna-a viva. Esperamos por si!', '2026-02-05 08:00:00', '2026-02-06 19:30:00', 'evento_693ae242763f6.jpg', 1),
(3, 2, 'Gulbenkian à Noite: O Jardim Digital', 'Para celebrar a nova fase do Centro de Arte Moderna (CAM), o parque transforma-se numa tela a céu aberto. Instalações de luz flutuantes reagem à presença dos visitantes nos trilhos, enquanto sensores colocados nas árvores convertem o vento em melodias suaves. O ponto alto é uma projeção holográfica sobre o lago principal, recriando peças da Coleção do Fundador em \"poeira estelar\", permitindo ver a arte clássica libertar-se das paredes do museu e habitar a natureza. A entrada é livre, mas sujeita à lotação do espaço.', '2025-12-16 22:30:00', '2025-12-12 01:30:00', 'evento_693af3cee3a28.jpg', 1),
(43, 8, 'stets', 'jgtirjgtegii', '2026-02-19 10:00:00', '2026-02-19 05:00:00', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `favorito`
--

CREATE TABLE `favorito` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `horario`
--

CREATE TABLE `horario` (
  `segunda` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quarta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quinta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sabado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domingo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `horario`
--

INSERT INTO `horario` (`segunda`, `terca`, `quarta`, `quinta`, `sexta`, `sabado`, `domingo`, `id`) VALUES
('09:00-18:00', '09:00-18:00', '09:00-18:00', '09:00-18:00', '09:00-18:00', '', '', 2),
('', '', '', '', '', '', '', 4),
('', '', '', '', '', '', '', 5),
('', '', '', '', '', '', '', 6),
('', '', '', '', '', '', '', 7),
('09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', 'Encerrado', 'Encerrado', 8),
('', '', '', '', '', '', '', 9),
('', '', '', '', '', '', '', 10),
('', '', '', '', '', '', '', 11),
('', '', '', '', '', '', '', 12),
('', '', '', '', '', '', '', 13),
('09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', 14),
('Fechado', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', 'Fechado', 15),
('09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '10:00 - 16:00', 'Fechado', 16),
('10:00 - 18:00', 'Fechado', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', 'Fechado', 'Fechado', 17),
('Encerrado', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', 'Fechado', 18),
('Encerrado', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', '10:00 - 18:00', 'Fechado', 19),
('Encerrado', '10:00 - 17:30', '10:00 - 17:30', '10:00 - 17:30', '10:00 - 17:30', '10:00 - 17:30', 'Fechado', 20),
('Encerrado', '09:30 - 17:30', '09:30 - 17:30', '09:30 - 17:30', '09:30 - 17:30', '09:30 - 17:30', 'Fechado', 21),
('09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', '09:00 - 18:00', 'Fechado', 22),
('09:00 - 19:00', '09:00 - 19:00', '09:00 - 19:00', '09:00 - 19:00', '09:00 - 19:00', '09:00 - 19:00', 'Fechado', 23),
('09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', 'Fechado', 24),
('09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', '09:30 - 18:30', 25);

-- --------------------------------------------------------

--
-- Table structure for table `linha_reserva`
--

CREATE TABLE `linha_reserva` (
  `id` int(11) NOT NULL,
  `reserva_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tipo_bilhete_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `local_cultural`
--

CREATE TABLE `local_cultural` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `morada` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distrito_id` int(11) NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto_telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem_principal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `horario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `local_cultural`
--

INSERT INTO `local_cultural` (`id`, `nome`, `tipo_id`, `morada`, `distrito_id`, `descricao`, `contacto_telefone`, `contacto_email`, `website`, `imagem_principal`, `ativo`, `latitude`, `longitude`, `horario_id`) VALUES
(1, 'Museu Nacional de Arte Antiga', 1, 'R. das Janelas Verdes, 1249-017', 11, 'O Museu Nacional de Arte Antiga é o mais importante museu de arte dos séculos XII a XIX em Portugal, ao acolher a mais relevante coleção pública de arte antiga do país. As suas colecções — cerca de 40 000 peças — incluem pintura, escultura, desenho e artes decorativas europeias e, também, colecções de arte asiática (Índia, China, Japão, etc.) e africana (marfins afro-portugueses) representativas das relações que se estabeleceram entre a Europa e o Oriente na sequência das viagens dos descobrimentos - iniciadas no século XV e de que Portugal foi nação pioneira.\r\n\r\nO museu encontra-se localizado num palácio dos finais do século XVII, mandado construir por D. Francisco de Távora, primeiro conde de Alvor. O Palácio é conhecido como Palácio de Alvor-Pombal pois, em 1759, após o Processo dos Távoras, o edifício foi adquirido em leilão por Paulo de Carvalho e Mendonça, irmão de Marquês de Pombal que, por morte do primeiro, passou a ser proprietário do palácio. Em 1879 o palácio foi alugado, e posteriormente adquirido, pelo Estado português para nele instalar o Museu Nacional de Bellas Artes e Arqueologia, inaugurado oficialmente em 11 de maio de 1884.\r\n\r\nO palácio confinava a oeste com o Convento de Santo Alberto, primeiro mosteiro de freiras carmelitas descalças em Lisboa, cujo patrono era Santo Alberto, razão pela qual era também conhecido por Convento das Albertas. Em 1890, aquando da morte da última freira, o estado toma posse do Convento de Santo Alberto, entregando em 1891 a sua tutela ao museu pois já na altura era reconhecida a necessidade de aumentar o espaço físico do mesmo. Derrubado o Convento, no seu lugar foi construído o edifício poente, também conhecido como \"anexo\", inaugurado em 1940 com a exposição \"Primitivos Portugueses\". ', '(+351) 21 391 2800', 'mnaa@mnaa.dgpc.pt', 'http://www.museudearteantiga.pt/', 'local_693adf6617bd8.jpg', 1, 38.7043, -9.16228, 16),
(2, 'Museu Calouste Gulbenkian', 1, 'Av. de Berna 45A, 1067-001 Lisboa', 11, 'O Museu Calouste Gulbenkian é um dos centros culturais mais importantes de Portugal, funcionando como um oásis no centro de Lisboa onde a arte, a arquitetura moderna e a natureza se fundem.\r\n\r\nO complexo divide-se em três pilares:\r\n\r\nA Coleção do Fundador: Reúne 6.000 peças de arte antiga, desde o Egito e a Pérsia até à pintura europeia (Rembrandt, Rubens) e as famosas joias de René Lalique.\r\n\r\n        Nota: Este edifício encontra-se encerrado para renovação até 2026.\r\n\r\n    Centro de Arte Moderna (CAM): Recentemente renovado pelo arquiteto Kengo Kuma, foca-se na arte moderna e contemporânea, com destaque para grandes nomes portugueses como Amadeo de Souza-Cardoso e Paula Rego.\r\n\r\nOs Jardins: Um parque emblemático da arquitetura paisagista, com lagos, trilhos e uma densa vegetação. É um refúgio de biodiversidade e tranquilidade, com entrada gratuita.', '21 782 30 00', 'museu@gulbenkian.pt', 'https://gulbenkian.pt/', 'local_693af147be689.jpg', 1, 38.7353, -9.1525, 17),
(3, 'Museu Nacional dos Coches', 1, 'Av. da Índia 136, 1300-300 Lisboa', 11, 'Localizado em Belém, o Museu Nacional dos Coches guarda a coleção de carruagens reais mais importante e rica do mundo, ilustrando a ostentação e a técnica do transporte entre os séculos XVI e XIX.\r\n\r\nA visita divide-se em dois edifícios vizinhos:\r\n\r\nO Novo Museu: Um edifício moderno e amplo (inaugurado em 2015), onde se encontra a grande maioria das viaturas.\r\nO Antigo Picadeiro Real: O salão original do século XVIII, que vale a visita pela sua arquitetura barroca e decoração luxuosa.\r\n\r\nDestaques imperdíveis:\r\n\r\nCoche dos Oceanos: Uma imponente obra-prima do barroco em talha dourada, símbolo do poder de D. João V.\r\nLandau do Regicídio: A carruagem histórica onde o Rei D. Carlos I foi assassinado em 1908, ainda com as marcas das balas.\r\n\r\nEm resumo: É o local onde a história de Portugal se conta através de verdadeiros \"palácios sobre rodas\".', '210 49 24 00', 'mncoches@imc-ip.pt', 'http://museudoscoches.gov.pt', 'local_693af56f02860.jpg', 1, 38.6986, -9.2005, 18),
(4, 'Museu Nacional de Soares dos Reis', 1, 'Rua de Dom Manuel II, 4050-342 Porto', 13, 'Localizado no Porto, no imponente Palácio dos Carrancas, este é o primeiro museu público de arte fundado em Portugal (1833), sendo a principal referência de Belas-Artes no norte do país.\r\n\r\nA coleção distingue-se em três áreas principais:\r\n\r\nEscultura: É o \"coração\" do museu, dedicado ao seu patrono, António Soares dos Reis, um dos maiores escultores portugueses do século XIX.\r\n\r\nPintura: Foca-se no naturalismo português, com obras de mestres como Henrique Pousão, Silva Porto e Marques de Oliveira.\r\n\r\n Artes Decorativas: Uma vasta coleção de cerâmica, vidros, ourivesaria e mobiliário que reflete o gosto e a produção artística dos séculos XVIII e XIX.\r\n\r\nDestaque imperdível:\r\n\r\n\"O Desterrado\" (Soares dos Reis): A obra-prima do escultor, uma estátua em mármore que combina uma beleza clássica com uma profunda melancolia (\"saudade\").\r\n\r\nEm resumo: É uma paragem obrigatória no Porto para quem aprecia a escultura romântica e a elegância de um palácio neoclássico do século XVIII.', '223 39 37 70', 'mnsr@imc-ip.pt', 'http://www.museusoaresdosreis.gov.pt', 'local_693afa1722cd1.jpg', 1, 41.1475, -8.6286, 19),
(5, 'Museu do Fado', 1, 'Largo do Chafariz de Dentro 1, 1100-139 Lisboa', 11, 'Situado à entrada de Alfama, o Museu do Fado celebra a canção urbana de Lisboa e o seu estatuto de Património Cultural Imaterial da Humanidade (UNESCO).\r\n\r\nO museu traça a história do género, desde as tabernas marginais do século XIX até aos grandes palcos, através de um percurso interativo e sonoro.\r\n\r\nDestaques imperdíveis:\r\n\r\nA Obra: O célebre quadro \"O Fado\" (1910) de José Malhoa, a imagem mais famosa desta cultura.\r\n\r\nOs Ícones: O espólio de lendas como Amália Rodrigues e Carlos do Carmo (xailes, vestidos, prémios).\r\n\r\n Instrumentos: A evolução histórica da guitarra portuguesa.\r\n\r\nEm resumo: É o ponto de partida ideal para compreender a \"alma\" de Lisboa antes de ir jantar a uma casa de fados no bairro.', '218 82 34 70', 'info@museudofado.pt', 'http://www.museudofado.pt', 'local_693afb73903d7.jpg', 1, 38.714, -9.128, 15),
(6, 'Torre de Belém', 2, 'Av. Brasília, 1400-038 Lisboa', 11, 'Símbolo máximo da \"Era dos Descobrimentos\" e Património Mundial da UNESCO, a Torre de Belém (1514-1520) é o cartão-postal mais famoso de Lisboa.\r\n\r\nConstruída sobre o rio Tejo, servia simultaneamente como fortaleza defensiva e \"porta cerimonial\" para os navegadores. É a obra-prima do estilo Manuelino, exibindo uma decoração rica em cordas, nós e esferas armilares esculpidas na pedra.\r\n\r\nDestaques imperdíveis:\r\n\r\nO Rinoceronte: Uma pequena escultura numa das guaritas exteriores, considerada a primeira representação deste animal na arte europeia.\r\n\r\nO Baluarte e o Terraço: A parte inferior com os canhões virados para o rio e o topo que oferece uma vista panorâmica única.\r\n\r\nNota: O primeiro Domingo do mês é entrada gratis!\r\n\r\nEm resumo: É uma joia de pedra sobre a água, que marca o auge do império marítimo português.', '213 62 00 34', 'torre.belem@dgpc.pt', 'http://www.torrebelem.gov.pt', 'local_693bf61f68a84.jpg', 1, 38.6916, -9.216, 20),
(7, 'Mosteiro dos Jerónimos', 2, 'Praça do Império 1400-206 Lisboa', 11, 'Obra-prima absoluta da arquitetura Manuelina e Património Mundial da UNESCO, o Mosteiro dos Jerónimos (iniciado em 1501) é o grande monumento erguido para celebrar o sucesso das viagens marítimas portuguesas à Índia.\r\n\r\nA visita foca-se em dois espaços grandiosos:\r\n\r\nA Igreja (Santa Maria de Belém): Impressiona pela sua abóbada única, suportada por colunas finas que se assemelham a palmeiras gigantes de pedra.\r\n\r\nO Claustro: Considerado um dos mais belos do mundo, é um pátio de dois andares onde a pedra foi esculpida com tal detalhe que parece renda, misturando símbolos religiosos e marítimos.\r\n\r\nDestaques imperdíveis (Panteão Nacional):\r\n\r\nAqui repousam as figuras máximas da história de Portugal: o navegador Vasco da Gama e o poeta Luís de Camões (na igreja), bem como o escritor Fernando Pessoa (no claustro).\r\n\r\nEm resumo: É a manifestação física, em pedra, da imensa riqueza e glória de Portugal durante a Era dos Descobrimentos.', '213 62 00 34', 'mosteiro.jeronimos@dgpc.pt', 'http://www.mosteirojeronimos.gov.pt', 'local_693bf752a3bba.jpg', 1, 38.6979, -9.2064, 21),
(8, 'Castelo de São Jorge', 2, 'Rua de Santa Cruz do Castelo, 1100-129 Lisboa', 11, 'Dominando a colina mais alta da cidade, o Castelo de São Jorge é uma imponente fortaleza militar (século XI) e o miradouro por excelência de Lisboa.\r\n\r\nConquistado aos mouros pelo primeiro rei de Portugal em 1147, o espaço destaca-se não por interiores luxuosos, mas pela sua estrutura defensiva bruta e localização estratégica.\r\n\r\nDestaques imperdíveis:\r\n\r\nAs Muralhas: Caminhar sobre as ameias e subir às torres para vistas de 360º sobre a cidade e o rio.\r\n\r\nO Miradouro: A varanda principal oferece a vista mais icónica sobre a Baixa Pombalina e o Tejo.\r\n\r\nA Câmara Escura: Um sistema ótico (periscópio) numa das torres que permite espiar a cidade em tempo real e 360º.\r\n\r\nNota curiosa: Os jardins são habitados por dezenas de pavões que circulam livremente entre os visitantes.\r\n\r\nEm resumo: É o local onde Lisboa \"nasceu\", ideal para compreender a geografia da cidade e ver o melhor pôr do sol.', '218 80 06 20', 'info@castelodesaojorge.pt', 'http://www.castelodesaojorge.pt', 'local_693bf81499d4e.jpg', 1, 38.7139, -9.1334, 22),
(9, 'Torre dos Clérigos', 2, 'Rua de São Filipe de Nery, 4050-546 Porto', 13, 'O verdadeiro ex-libris da cidade do Porto, a Torre dos Clérigos é uma obra-prima do Barroco (século XVIII), desenhada pelo arquiteto italiano Nicolau Nasoni.\r\n\r\nVisível de quase qualquer ponto da cidade, o monumento é composto por uma igreja de planta oval e pela icónica torre sineira de 75 metros de altura.\r\n\r\nDestaques imperdíveis:\r\n\r\nO Desafio: Subir os 225 degraus da escadaria em espiral estreita.\r\n\r\nA Recompensa: O topo oferece a melhor vista panorâmica de 360º sobre o Porto, o Rio Douro e as caves de Gaia.\r\n\r\nEm resumo: É a bússola da cidade e a paragem obrigatória para quem quer ver o Porto \"aos seus pés\".', '220 14 54 89', 'geral@torredosclerigos.pt', 'https://torredosclerigos.pt', 'local_693bfa564fccf.jpg', 1, 41.1458, -8.6145, 23),
(10, 'Palácio Nacional de Sintra', 2, 'Largo Rainha Dona Amélia, 2710-616 Sintra', 11, 'No coração do centro histórico, distingue-se imediatamente pelas suas duas gigantescas chaminés cónicas brancas. É o palácio real medieval mais bem preservado de Portugal, tendo sido habitado pela família real de forma quase contínua desde o século XV até 1910.\r\n\r\nA sua arquitetura é uma fusão única de estilos gótico, manuelino e mudéjar (influência islâmica), albergando a mais importante coleção de azulejos hispano-mouriscos da Europa.\r\n\r\nDestaques imperdíveis:\r\n\r\nSala dos Brasões: O ponto alto da visita. Uma cúpula dourada impressionante decorada com os brasões de 72 famílias nobres portuguesas, com o Rei D. Manuel I no topo.\r\n\r\nSala dos Cisnes: O grande salão de banquetes, cujo teto está pintado com 27 cisnes com coroas de ouro.\r\n\r\nSala das Pegas: Famosa pela pintura de pegas no teto, encomendada pelo Rei D. João I como resposta satírica aos rumores da corte (as pegas representam a tagarelice).\r\n\r\nEm resumo: É a \"casa-mãe\" de Sintra, essencial para quem gosta de azulejaria, heráldica e história medieval.', '219 23 73 00', 'pnsintra@parquesdesintra.pt', 'https://www.parquesdesintra.pt', 'local_693bfb450f617.jpg', 1, 38.7978, -9.3905, 24),
(11, 'Ruínas Romanas de Conímbriga', 3, 'Conímbriga, 3150-220 Condeixa-a-Nova', 6, 'Localizadas em Condeixa-a-Nova (perto de Coimbra), são o maior e mais bem preservado complexo arqueológico romano em Portugal.\r\n\r\nConímbriga foi uma cidade próspera do Império que floresceu até ser abandonada devido às invasões bárbaras, o que paradoxalmente ajudou a preservar as suas estruturas até hoje.\r\n\r\nDestaques imperdíveis:\r\n\r\nCasa dos Repuxos: A grande atração. Uma mansão nobre onde o sistema hidráulico original foi restaurado, permitindo ver os jardins e as fontes de água a funcionar tal como há 2.000 anos.\r\n\r\nOs Mosaicos: O local possui uma das melhores coleções de mosaicos de chão da Europa, incrivelmente coloridos e detalhados, com cenas mitológicas (como o Labirinto do Minotauro).\r\n\r\nMuralha Defensiva: Uma parede maciça construída \"à pressa\" que cortou a cidade ao meio numa tentativa desesperada de travar os invasores.\r\n\r\nNota: O bilhete inclui a visita às ruínas (ao ar livre) e ao Museu Monográfico, que expõe os objetos do dia a dia encontrados nas escavações.', '239 94 11 77', 'conimbriga@conimbriga.pt', 'http://www.conimbriga.gov.pt', 'local_6967c37b88e4e.jpg', 1, 40.099, -8.4933, 25);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1761049888),
('m130524_201442_init', 1761049892),
('m190124_110200_add_verification_token_column_to_user_table', 1761049892),
('m251021_160653_init_rbac', 1761130054),
('m140506_102106_rbac_init', 1761130265),
('m170907_052038_rbac_add_index_on_auth_assignment_user_id', 1761130265),
('m180523_151638_rbac_updates_indexes_without_prefix', 1761130265),
('m200409_110543_rbac_update_mssql_trigger', 1761130265),
('m251022_105459_init_rbac', 1761130510),
('m251028_134151_create_admin_user', 1761658984);

-- --------------------------------------------------------

--
-- Table structure for table `noticia`
--

CREATE TABLE `noticia` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_publicacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `local_id` int(11) NOT NULL,
  `destaque` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `noticia`
--

INSERT INTO `noticia` (`id`, `titulo`, `conteudo`, `resumo`, `imagem`, `data_publicacao`, `ativo`, `local_id`, `destaque`) VALUES
(5, 'Museu Nacional de Arte Antiga encerra temporariamente para obras de requalificação', 'A Direção do Museu Nacional de Arte Antiga informa que, a partir desta data, o museu suspende as visitas presenciais para a realização de obras de fundo no edifício e nas galerias de exposição.\r\n\r\nEsta intervenção é essencial para garantir a preservação do nosso valioso acervo e para melhorar as condições de acolhimento aos visitantes. O projeto inclui a renovação dos sistemas de climatização, restauro de salas históricas e a reorganização do percurso expositivo.\r\n\r\nDurante o período de encerramento, continuaremos a partilhar a nossa arte através das plataformas digitais e de iniciativas pontuais fora de portas. Agradecemos a compreensão de todos e prometemos um regresso em grande, com um museu renovado e preparado para o futuro.', 'O MNAA fecha hoje as suas portas ao público para dar início a um extenso projeto de conservação e modernização. A reabertura está prevista para o início de 2026.', 'noticia_693ae366e17f9.jpg', '2025-12-11 15:29:42', 1, 1, 1),
(6, 'O Tesouro Oculto de Lalique: Descoberta Inédita nas Obras da Gulbenkian', 'O mundo da arte foi surpreendido esta manhã com o anúncio de uma descoberta histórica no interior do Museu Calouste Gulbenkian: um \"caderno perdido\" do mestre joalheiro René Lalique foi encontrado intacto atrás de um painel de madeira falso durante as atuais obras de renovação. O achado, oculto há quase setenta anos, contém esboços detalhados e aguarelas para uma peça mítica nunca construída, a \"Fénix de Ouro\", desenhada exclusivamente para o próprio Calouste Gulbenkian como símbolo de amizade eterna. Enquanto os conservadores trabalham já na preservação dos frágeis documentos, a administração confirmou que este tesouro inédito será a peça central da grande reabertura do edifício em 2026, transformando um simples acidente de obra num dos momentos culturais mais aguardados da década em Lisboa.', 'Durante as atuais obras de renovação do Museu Calouste Gulbenkian, foi encontrado um caderno inédito de René Lalique escondido numa parede falsa. O documento contém esboços de uma joia lendária nunca fabricada, prometendo ser a grande atração da reabertura do museu.', 'noticia_693af2c10cf20.png', '2025-12-11 16:35:13', 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `data_visita` date NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `estado` enum('Expirado','Confirmada','Cancelada') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_bilhete`
--

CREATE TABLE `tipo_bilhete` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `local_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_bilhete`
--

INSERT INTO `tipo_bilhete` (`id`, `nome`, `descricao`, `preco`, `ativo`, `local_id`) VALUES
(5, 'Adulto', 'Adulto', 10.00, 1, 1),
(6, 'Kids', 'Kids', 5.00, 1, 1),
(7, 'Senior', 'Senior', 7.00, 1, 1),
(8, 'Estudante', 'Estudante', 1.50, 1, 1),
(9, 'Geral', 'Bilhete de acesso geral', 15.00, 1, 3),
(10, 'Criança(6-12)', 'Bilhete para crianças dos 6 aos 12 anos.', 5.00, 1, 3),
(11, 'Geral', 'Bilhete geral.', 10.00, 1, 4),
(12, 'Normal', 'Bilhete normal.', 5.00, 1, 5),
(13, 'Jovem(13-25)', 'Jovens.', 2.50, 1, 5),
(14, 'Estudantes do ensino superior (Corredor Cultural) +26 anos', 'Estudantes do ensino superior do Corredor Cultural com mais de 26 anos.', 4.00, 1, 5),
(15, 'Pessoas com necessidades específicas + acomp. gratuito', 'Pessoas com necessidades.', 3.50, 1, 5),
(16, 'Senior(+65)', 'Para pessoas com +65 anos.', 4.00, 1, 5),
(17, 'Crianças(até aos 12)', 'Crianças até aos 12 anos.', 0.00, 1, 5),
(18, 'Geral', 'Bilhete geral.', 15.00, 1, 6),
(19, 'Geral', 'Bilhete geral.', 18.00, 1, 7),
(20, 'Adulto', 'Bilhete para adultos.', 15.00, 1, 8),
(21, 'Jovem(13-25)', 'Bilhete para jovens entre os 13 e os 25 anos.', 7.50, 1, 8),
(22, 'Senior(>65)', 'Bilhete para seniores.', 12.50, 1, 8),
(23, 'Crianças(<12)', 'Entrada gratis a crianças até aos 12 anos.', 0.00, 1, 8),
(24, 'Geral', 'Bilhete geral.', 10.00, 1, 9),
(25, 'Estudantes', 'Bilhete para estudantes.', 7.00, 1, 9),
(26, 'Crianças(<10)', 'Crianças até aos 10 anos.', 0.00, 1, 9),
(27, 'Geral', 'Bilhete geral.', 13.00, 1, 10),
(28, 'Geral', 'Bilhete geral.', 13.00, 1, 11),
(30, 'Adulto', 'Bilhete para pessoas entre 18-64 anos\r\n', 7.00, 1, 2),
(31, 'Sênior', 'Bilhete para pessoas entre 65+ anos', 5.50, 1, 2),
(32, 'Criança', 'bilhete para pessoas entre 3-17 anos', 2.50, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_local`
--

CREATE TABLE `tipo_local` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_local`
--

INSERT INTO `tipo_local` (`id`, `nome`, `descricao`, `icone`) VALUES
(1, 'Museu', 'Instituições que conservam coleções de objetos de valor cultural', 'marker_693af88e9e359.svg'),
(2, 'Monumento', 'Estruturas ou construções de relevância histórica e cultural', 'marker_693af881e06b1.svg'),
(3, 'Ruina', 'Ruinas', 'marker_693af89f6d192.svg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(8, 'admin', 'MhmeNGvy7wibfDGcik_kfq2RW8Tjx5bN', '$2y$13$454KQG/5Y.dsj2qOIcXQXOjpkbcYChkx7D2VYUb1n6dDX0EF27hQO', NULL, 'admin@maislusitania.pt', 10, 1761658984, 1773332624, NULL),
(10, 'user', 'mKGWu9rZ_IZENJ6pmOe0bXYQGB8UC_6n', '$2y$13$BaxuwN3LsiFrhdA4B0hy2OUIxyQydua9D6hisFBQQcLx/IIIvuL0K', NULL, 'user@user.com', 10, 1762961388, 1773332654, 'pL_uMRJGqvJj9nCAcTigqzFqqvwzgp3D_1762961388'),
(30, 'gestor', 't-7gOYLi60s1KWjoxmp2UBWMFbCwmI22', '$2y$13$/KejqEhAK9OqAbBe6.J/dOKCtXIvgK0tt4zGaNBhK7UzTHaOnjQCO', NULL, 'gestor@maislusitania.pt', 10, 1773332687, 1773332700, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `primeiro_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultimo_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem_perfil` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `primeiro_nome`, `ultimo_nome`, `imagem_perfil`, `user_id`) VALUES
(2, 'user', 'user', NULL, 10),
(3, 'Admin', 'admin', NULL, 8),
(23, 'gestor', 'gestor', NULL, 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx-auth_assignment-user_id` (`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `avaliacao_unica` (`local_id`,`utilizador_id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `idx_avaliacoes_local` (`local_id`);

--
-- Indexes for table `distrito`
--
ALTER TABLE `distrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indexes for table `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `local_id` (`local_id`),
  ADD KEY `idx_eventos_data` (`data_inicio`);

--
-- Indexes for table `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorito_unico` (`utilizador_id`,`local_id`),
  ADD KEY `local_id` (`local_id`);

--
-- Indexes for table `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `linha_reserva`
--
ALTER TABLE `linha_reserva`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_reserva_tipo_bilhete` (`reserva_id`,`tipo_bilhete_id`),
  ADD KEY `fk_linha_reserva_reservas` (`reserva_id`),
  ADD KEY `fk_linha_reserva_tipo_bilhete` (`tipo_bilhete_id`);

--
-- Indexes for table `local_cultural`
--
ALTER TABLE `local_cultural`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `local_cultural_unique` (`horario_id`),
  ADD KEY `idx_locais_distrito` (`distrito_id`),
  ADD KEY `idx_locais_tipo` (`tipo_id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_noticias_locais_culturais` (`local_id`);

--
-- Indexes for table `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservas_usuario` (`utilizador_id`),
  ADD KEY `idx_reservas_local` (`local_id`),
  ADD KEY `idx_reservas_data` (`data_visita`);

--
-- Indexes for table `tipo_bilhete`
--
ALTER TABLE `tipo_bilhete`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tipo_bilhete_local_cultural_idx` (`local_id`);

--
-- Indexes for table `tipo_local`
--
ALTER TABLE `tipo_local`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_profile_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `distrito`
--
ALTER TABLE `distrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evento`
--
ALTER TABLE `evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `favorito`
--
ALTER TABLE `favorito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `horario`
--
ALTER TABLE `horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `linha_reserva`
--
ALTER TABLE `linha_reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `local_cultural`
--
ALTER TABLE `local_cultural`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `noticia`
--
ALTER TABLE `noticia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tipo_bilhete`
--
ALTER TABLE `tipo_bilhete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tipo_local`
--
ALTER TABLE `tipo_local`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD CONSTRAINT `avaliacao_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avaliacoes_user` FOREIGN KEY (`utilizador_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `favorito_ibfk_2` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`utilizador_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `linha_reserva`
--
ALTER TABLE `linha_reserva`
  ADD CONSTRAINT `fk_linha_reserva_reservas` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`),
  ADD CONSTRAINT `fk_linha_reserva_tipo_bilhete` FOREIGN KEY (`tipo_bilhete_id`) REFERENCES `tipo_bilhete` (`id`);

--
-- Constraints for table `local_cultural`
--
ALTER TABLE `local_cultural`
  ADD CONSTRAINT `fk_local_cultural_horario` FOREIGN KEY (`horario_id`) REFERENCES `horario` (`id`),
  ADD CONSTRAINT `local_cultural_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_local` (`id`),
  ADD CONSTRAINT `local_cultural_ibfk_2` FOREIGN KEY (`distrito_id`) REFERENCES `distrito` (`id`);

--
-- Constraints for table `noticia`
--
ALTER TABLE `noticia`
  ADD CONSTRAINT `fk_noticias_locais_culturais` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`);

--
-- Constraints for table `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reservas_user` FOREIGN KEY (`utilizador_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tipo_bilhete`
--
ALTER TABLE `tipo_bilhete`
  ADD CONSTRAINT `fk_tipo_bilhete_local_cultural` FOREIGN KEY (`local_id`) REFERENCES `local_cultural` (`id`);

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_user_profile_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
