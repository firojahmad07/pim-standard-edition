import {FileInfo} from 'akeneo-design-system';
import {NotificationLevel, Notify, Translate, ValidationError} from '@akeneo-pim-community/shared';
import {createLineFromFilename} from 'akeneoassetmanager/application/asset-upload/utils/line-factory';
import Line from 'akeneoassetmanager/application/asset-upload/model/line';
import {File as FileModel} from 'akeneoassetmanager/domain/model/file';
import {getThumbnailFromFile, uploadFile} from 'akeneoassetmanager/application/asset-upload/utils/file';
import {
  fileThumbnailGenerationDoneAction,
  fileUploadFailureAction,
  fileUploadProgressAction,
  fileUploadStartAction,
  fileUploadSuccessAction,
  linesAddedAction,
} from 'akeneoassetmanager/application/asset-upload/reducer/action';
import createQueue from 'p-limit';
import {AssetFamily} from 'akeneoassetmanager/domain/model/asset-family/asset-family';
import Channel from 'akeneoassetmanager/domain/model/channel';
import Locale from 'akeneoassetmanager/domain/model/locale';

const CONCURRENCY = 5;
const queue = createQueue(CONCURRENCY);

const queuedFiles: {[key: string]: File} = {};

const getCurrentQueuedFiles = () => queuedFiles;

const uploadAndDispatch = (
  uploader: (file: File, onProgress: (ratio: number) => void) => Promise<FileInfo>,
  line: Line,
  file: File,
  dispatch: (action: any) => void
) => {
  queue(() => {
    queuedFiles[line.id] = file;

    dispatch(fileUploadStartAction(line));

    return uploadFile(uploader, file, line, (line: Line, progress: number) => {
      dispatch(fileUploadProgressAction(line, progress));
    });
  })
    .then((file: FileModel) => {
      delete queuedFiles[line.id];
      dispatch(fileUploadSuccessAction(line, file));
    })
    .catch((errors: ValidationError[]) => {
      dispatch(fileUploadFailureAction(line, errors));
    });
};

const onFileDrop = (
  uploader: (file: File, onProgress: (ratio: number) => void) => Promise<FileInfo>,
  files: File[],
  assetFamily: AssetFamily,
  channels: Channel[],
  locales: Locale[],
  dispatch: (action: any) => void
) => {
  if (null === files || 0 === files.length) {
    return;
  }

  const lines = files.map((file: File) => {
    const filename = file.name;

    const line = createLineFromFilename(filename, assetFamily, channels, locales);
    getThumbnailFromFile(file, line).then(({thumbnail, line}) =>
      dispatch(fileThumbnailGenerationDoneAction(thumbnail, line))
    );

    uploadAndDispatch(uploader, line, file, dispatch);

    return line;
  });

  dispatch(linesAddedAction(lines));
};

const retryFileUpload = (
  notify: Notify,
  translate: Translate,
  uploader: (file: File, onProgress: (ratio: number) => void) => Promise<FileInfo>,
  line: Line,
  dispatch: (action: any) => void
) => {
  const file = queuedFiles[line.id];

  if (!file) {
    notify(NotificationLevel.ERROR, translate('pim_asset_manager.asset.upload.cannot_retry'));
    return;
  }

  uploadAndDispatch(uploader, line, file, dispatch);
};

export {getCurrentQueuedFiles, retryFileUpload, onFileDrop};
