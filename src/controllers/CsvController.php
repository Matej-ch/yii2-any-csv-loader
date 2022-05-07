<?php

namespace matejch\anyCsvLoader\controllers;

use matejch\anyCsvLoader\models\CsvMap;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\ContentNegotiator;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CsvController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'load-map'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'negotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['load-map', 'save-map'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'load-map' => ['GET'],
                    'save-map' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'maps' => ArrayHelper::map(CsvMap::find()->select('id,name')->all(), 'id', 'name')
        ]);
    }

    public function actionLoadMap($id)
    {
        try {
            $model = $this->findModel($id);
        } catch (NotFoundHttpException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'map' => $model->values];
    }

    public function actionSaveMap()
    {
        return ['success' => false];
    }

    /**
     * @param $id
     * @return CsvMap|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?CsvMap
    {
        if (($model = CsvMap::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('anyCsvLoader/view', 'Not found'));
    }
}