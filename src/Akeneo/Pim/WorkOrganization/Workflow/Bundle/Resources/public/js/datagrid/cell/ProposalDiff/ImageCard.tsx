import React, {cloneElement, isValidElement} from 'react';
import {
  Button,
  DownloadIcon,
  FullscreenIcon,
  getColor,
  IconButton,
  MediaFileInput,
  useBooleanState,
} from '@akeneo-pim-community/akeneo-design-system';
import {useTranslate} from '@akeneo-pim-community/shared';
import {FullscreenPreview} from '@akeneo-pim-community/akeneo-design-system/src/storybook';
import styled from 'styled-components';

const AddedClassName = 'ImageCard--added';
const RemovedClassName = 'ImageCard--removed';
const ImageCardWrapper = styled.div`
  margin-top: 5px;

  & > .${AddedClassName} {
    background: ${getColor('green', 20)};
  }
  & > .${RemovedClassName} {
    background: ${getColor('red', 20)};
  }
`;

type ImageCardProps = {
  thumbnailUrl?: string;
  filePath: string;
  originalFilename: string;
  downloadUrl?: string;
  state?: 'removed' | 'added';
};

const ImageCard: React.FC<ImageCardProps> = ({
  thumbnailUrl = '/bundles/pimui/img/image_default.png',
  filePath,
  originalFilename,
  downloadUrl,
  state,
  children,
  ...rest
}) => {
  const [isFullscreenModalOpen, openFullscreenModal, closeFullscreenModal] = useBooleanState();
  const translate = useTranslate();

  let className = undefined;
  if (state) {
    className = {
      removed: RemovedClassName,
      added: AddedClassName,
    }[state];
  }

  let fullscreen = (
    <FullscreenPreview title={originalFilename} src={thumbnailUrl} onClose={closeFullscreenModal}>
      <Button href={thumbnailUrl} ghost={true} level="tertiary" target="_blank" download={thumbnailUrl}>
        <DownloadIcon /> {translate('pim_asset_manager.asset_preview.download')}
      </Button>
    </FullscreenPreview>
  );

  if (React.Children.count(children)) {
    fullscreen = (
      <>
        {React.Children.map(children, child => {
          return isValidElement(child) && cloneElement(child, {onClose: closeFullscreenModal});
        })}
      </>
    );
  }

  return (
    <div {...rest}>
      <ImageCardWrapper>
        <MediaFileInput
          onChange={() => {}}
          clearTitle={''}
          uploader={() => {
            return Promise.resolve({filePath: '', originalFilename: ''});
          }}
          uploadErrorLabel={''}
          uploadingLabel={''}
          size="small"
          thumbnailUrl={thumbnailUrl}
          value={{filePath, originalFilename}}
          clearable={false}
          className={className}
        >
          {downloadUrl && (
            <IconButton
              download={downloadUrl}
              href={downloadUrl}
              icon={<DownloadIcon />}
              target="_blank"
              title={translate('pim_asset_manager.asset_preview.download')}
            />
          )}
          {thumbnailUrl !== '/bundles/pimui/img/image_default.png' && (
            <IconButton
              icon={<FullscreenIcon />}
              onClick={openFullscreenModal}
              title={translate('pim_asset_manager.asset.button.fullscreen')}
            />
          )}
        </MediaFileInput>
      </ImageCardWrapper>
      {thumbnailUrl !== '/bundles/pimui/img/image_default.png' && isFullscreenModalOpen && fullscreen}
    </div>
  );
};

export {ImageCard};
